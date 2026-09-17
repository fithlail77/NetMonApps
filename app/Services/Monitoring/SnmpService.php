<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Log;

class SnmpService
{
    private int $timeout;
    private int $retries;

    public function __construct(int $timeout = 10, int $retries = 2)
    {
        $this->timeout = $timeout;
        $this->retries = $retries;
    }

    public function get(string $ip, string $oid, string $community = 'public', string $version = 'v2c'): ?string
    {
        try {
            $func = $version === 'v1' ? 'snmpget' : ($version === 'v3' ? 'snmp3_get' : 'snmp2_get');
            $args = [$ip, $community, $oid, $this->timeout * 1000000, $this->retries];

            $result = @call_user_func_array($func, $args);

            if ($result === false) {
                return null;
            }

            return $this->parseValue($result);
        } catch (\Exception $e) {
            Log::warning("SNMP GET failed for {$ip}", ['oid' => $oid, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function walk(string $ip, string $oid, string $community = 'public', string $version = 'v2c'): array
    {
        try {
            $func = $version === 'v1' ? 'snmpwalk' : ($version === 'v3' ? 'snmp3_walk' : 'snmp2_walk');
            $args = [$ip, $community, $oid, $this->timeout * 1000000, $this->retries];

            $result = @call_user_func_array($func, $args);

            return $result ?: [];
        } catch (\Exception $e) {
            Log::warning("SNMP WALK failed for {$ip}", ['oid' => $oid, 'error' => $e->getMessage()]);
            return [];
        }
    }

    public function getCpuUsage(string $ip, string $community = 'public', string $version = 'v2c'): ?float
    {
        $oids = [
            '.1.3.6.1.4.1.14988.1.1.3.1.0',
            '.1.3.6.1.4.1.14988.1.1.3.10.0',
            '.1.3.6.1.2.1.25.3.3.1.2.1',
        ];

        foreach ($oids as $oid) {
            $value = $this->get($ip, $oid, $community, $version);
            if ($value !== null && is_numeric($value)) {
                $num = (float) $value;
                if ($num > 100) {
                    $num = $num / 10;
                }
                return min($num, 100);
            }
        }

        $walk = $this->walk($ip, '.1.3.6.1.2.1.25.3.3.1.2', $community, $version);
        if (! empty($walk)) {
            $total = 0;
            $count = 0;
            foreach ($walk as $val) {
                $num = (float) preg_replace('/[^0-9.]/', '', $val);
                if ($num > 0) {
                    $total += $num;
                    $count++;
                }
            }
            if ($count > 0) {
                return round($total / $count, 2);
            }
        }

        return null;
    }

    public function getMemoryUsage(string $ip, string $community = 'public', string $version = 'v2c'): ?float
    {
        $totalOid = '.1.3.6.1.4.1.14988.1.1.3.3.0';
        $freeOid = '.1.3.6.1.4.1.14988.1.1.3.4.0';

        $total = $this->get($ip, $totalOid, $community, $version);
        $free = $this->get($ip, $freeOid, $community, $version);

        if ($total !== null && $free !== null && (float) $total > 0) {
            return round(((float) $total - (float) $free) / (float) $total * 100, 2);
        }

        $totalOid = '.1.3.6.1.2.1.25.2.2.0';
        $walk = $this->walk($ip, '.1.3.6.1.2.1.25.2.3.1.5', $community, $version);
        $walkUsed = $this->walk($ip, '.1.3.6.1.2.1.25.2.3.1.6', $community, $version);

        if (! empty($walk) && ! empty($walkUsed)) {
            $totalMem = 0;
            $usedMem = 0;
            foreach ($walk as $val) {
                $totalMem += (float) preg_replace('/[^0-9.]/', '', $val);
            }
            foreach ($walkUsed as $val) {
                $usedMem += (float) preg_replace('/[^0-9.]/', '', $val);
            }
            if ($totalMem > 0) {
                return round($usedMem / $totalMem * 100, 2);
            }
        }

        return null;
    }

    public function getUptime(string $ip, string $community = 'public', string $version = 'v2c'): ?string
    {
        $oid = '.1.3.6.1.2.1.1.3.0';
        return $this->get($ip, $oid, $community, $version);
    }

    public function getSystemName(string $ip, string $community = 'public', string $version = 'v2c'): ?string
    {
        $oid = '.1.3.6.1.2.1.1.5.0';
        return $this->get($ip, $oid, $community, $version);
    }

    private function parseValue(string $raw): ?string
    {
        $raw = trim($raw);
        $raw = preg_replace('/^"(.*)"$/s', '$1', $raw);

        if (preg_match('/^(?:INTEGER|Gauge32|Counter32|Counter64|Timeticks|OID|IpAddress|Opaque|Bits|NSAPADDRESS):?\s*(.+)$/i', $raw, $m)) {
            $val = trim($m[1]);
            if (preg_match('/^\((\d+)\)\s*.+$/', $val, $tm)) {
                return $tm[1];
            }
            return $val;
        }

        return $raw;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }
}
