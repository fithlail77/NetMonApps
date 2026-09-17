<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Log;

class PingService
{
    private int $timeout;
    private int $packets;

    public function __construct(int $timeout = 5, int $packets = 3)
    {
        $this->timeout = $timeout;
        $this->packets = $packets;
    }

    public function ping(string $ip): array
    {
        $os = strtolower(PHP_OS_FAMILY);
        $timeoutFlag = $os === 'windows' ? '-n' : '-W';
        $countFlag = $os === 'windows' ? '-n' : '-c';
        $ttlFlag = $os === 'windows' ? '-i' : '-t';

        $command = sprintf(
            'ping %s %d %s %d %s 2>&1',
            $countFlag,
            $this->packets,
            $timeoutFlag,
            $this->timeout,
            escapeshellarg($ip)
        );

        $output = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        $result = [
            'ip' => $ip,
            'status' => $returnCode === 0 ? 'up' : 'down',
            'latency' => null,
            'packet_loss' => null,
            'raw_output' => implode("\n", $output),
        ];

        if ($returnCode === 0) {
            $result = $this->parsePingOutput($output, $result);
        }

        Log::info("Ping {$ip}", ['status' => $result['status'], 'latency' => $result['latency']]);

        return $result;
    }

    private function parsePingOutput(array $output, array $result): array
    {
        $text = implode("\n", $output);

        if (preg_match('/avg\s*=\s*[\d.]+\/([\d.]+)\/[\d.]+/', $text, $matches)) {
            $result['latency'] = (float) $matches[1];
        } elseif (preg_match('/time[=:](\d+)ms/', $text, $matches)) {
            $result['latency'] = (float) $matches[1];
        }

        if (preg_match('/(\d+)%\s*(?:packet loss|loss)/', $text, $matches)) {
            $result['packet_loss'] = (float) $matches[1];
        }

        return $result;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setPackets(int $packets): self
    {
        $this->packets = $packets;
        return $this;
    }
}
