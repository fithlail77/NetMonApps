<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceMetric;
use App\Models\DeviceStatusLog;
use App\Services\Alerting\AlertService;
use App\Services\Monitoring\MonitoringService;
use App\Services\Monitoring\PingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class AlertSimulateDown extends Command
{
    protected $signature = 'alert:simulate-down
        {device : Device ID or exact name}
        {--send : Deliver real Telegram/Email instead of faking the channels}
        {--keep : Leave device status as down and keep the generated alert}';

    protected $description = 'Simulate a device going down and verify the full alert -> Telegram/Email path';

    public function handle(AlertService $alertService): int
    {
        $device = $this->resolveDevice((string) $this->argument('device'));
        if (! $device) {
            return self::FAILURE;
        }

        $fakeChannels = ! $this->option('send');
        $mailSpy = null;

        $this->laravel->instance(PingService::class, $this->fakePingService());
        $this->info('Ping faked: device always answers down (no real network call).');

        if ($fakeChannels) {
            Http::fake([
                'api.telegram.org/*' => Http::response(['ok' => true, 'result' => []]),
            ]);
            $mailSpy = $this->swapMailSpy();
            $this->info('Channels faked: Telegram (Http::fake), Email (Mail swap).');
        } else {
            $this->warn('LIVE MODE: real Telegram message and SMTP email will be sent.');
        }

        $monitoring = $this->laravel->make(MonitoringService::class);

        $previousStatus = $device->status;
        $this->line("Device: {$device->name} ({$device->ip_address}) status: {$previousStatus}");

        if ($previousStatus !== 'up') {
            $device->update(['status' => 'up']);
            $this->line("Forced status {$previousStatus} -> up so the transition fires.");
        }

        $blockedAlerts = $device->alerts()->whereIn('status', ['triggered', 'acknowledged'])->count();
        if ($blockedAlerts > 0) {
            $alertService->resolveDeviceAlerts($device);
            $this->line("Resolved {$blockedAlerts} active alert(s) first (dedup would skip a new alert).");
        }

        $startedAt = now();

        $this->info('Running MonitoringService::checkDevice (ping -> status -> event -> alert -> notify)...');
        $result = $monitoring->checkDevice($device->fresh());

        $this->line("Status transition: {$result['previous_status']} -> {$result['status']}");

        $alert = $device->alerts()->where('triggered_at', '>=', $startedAt)->latest('id')->first();

        $failed = false;

        if (! $alert) {
            $this->error('FAIL: no alert row created after down transition.');
            $failed = true;
        } else {
            $this->info("PASS: alert #{$alert->id} created [{$alert->severity}] {$alert->message}");
        }

        if ($fakeChannels) {
            $failed = ! $this->assertTelegram() || $failed;
            $failed = ! $this->assertEmail($mailSpy) || $failed;
        } else {
            $this->line('Delivery attempted live. Check storage/logs/laravel.log for channel errors.');
        }

        if (! $this->option('keep')) {
            $this->restore($device, $alert, $startedAt);
        } else {
            $this->warn('Device left DOWN and alert left TRIGGERED (--keep).');
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function resolveDevice(string $identifier): ?Device
    {
        $device = ctype_digit($identifier)
            ? Device::find((int) $identifier)
            : Device::where('name', $identifier)->first();

        if (! $device) {
            $this->error("Device \"{$identifier}\" not found. Use an ID or an exact name.");

            return null;
        }

        return $device;
    }

    private function fakePingService(): PingService
    {
        return new class extends PingService
        {
            public function ping(string $ip): array
            {
                return [
                    'ip' => $ip,
                    'status' => 'down',
                    'latency' => null,
                    'packet_loss' => 100,
                    'raw_output' => 'simulated: 100% packet loss',
                ];
            }
        };
    }

    private function swapMailSpy(): object
    {
        $spy = new class
        {
            public array $sent = [];

            public function raw($text, $callback)
            {
                $message = new class
                {
                    public ?string $subject = null;
                    public mixed $to = null;
                    public mixed $from = null;

                    public function subject(string $subject): static
                    {
                        $this->subject = $subject;

                        return $this;
                    }

                    public function to($address): static
                    {
                        $this->to = $address;

                        return $this;
                    }

                    public function from($address, $name = null): static
                    {
                        $this->from = $address;

                        return $this;
                    }
                };

                if (is_callable($callback)) {
                    $callback($message);
                }

                $this->sent[] = [
                    'to' => $message->to,
                    'subject' => $message->subject,
                    'body' => $text,
                ];
            }

            public function __call($method, $arguments)
            {
                //
            }
        };

        Mail::swap($spy);

        return $spy;
    }

    private function assertTelegram(): bool
    {
        $requests = Http::recorded(
            fn ($request) => str_contains($request->url(), 'api.telegram.org')
        );

        if ($requests->isEmpty()) {
            $this->error('FAIL: no Telegram API call recorded.');

            return false;
        }

        $body = $requests->last()[0]->data();
        $this->info('PASS: Telegram API called.');
        $this->line('  chat_id: ' . ($body['chat_id'] ?? '-'));
        $this->line('  text: ' . str_replace("\n", ' | ', (string) ($body['text'] ?? '')));

        return true;
    }

    private function assertEmail(object $mailSpy): bool
    {
        if (empty($mailSpy->sent)) {
            $this->error('FAIL: no email captured by mail spy.');

            return false;
        }

        $mail = end($mailSpy->sent);
        $this->info('PASS: email captured (SMTP not contacted).');
        $this->line('  to: ' . json_encode($mail['to']));
        $this->line('  subject: ' . ($mail['subject'] ?? '-'));
        $this->line('  body: ' . str_replace("\n", ' | ', (string) $mail['body']));

        return true;
    }

    private function restore(Device $device, $alert, $startedAt): void
    {
        $device->refresh()->update(['status' => 'up', 'last_seen_at' => now()]);

        DeviceStatusLog::create([
            'device_id' => $device->id,
            'status' => 'up',
            'changed_at' => now(),
        ]);

        if ($alert) {
            $alert->update(['status' => 'resolved', 'resolved_at' => now()]);
        }

        DeviceMetric::where('device_id', $device->id)
            ->where('recorded_at', '>=', $startedAt)
            ->delete();

        $this->line('Restored: status up, alert resolved, simulated metrics removed.');
    }
}
