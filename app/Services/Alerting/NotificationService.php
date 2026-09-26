<?php

namespace App\Services\Alerting;

use App\Models\Alert;
use App\Models\Device;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendAlertNotification(Alert $alert): void
    {
        $alert->load('device', 'alertRule');

        if (Setting::getValue('alert_email_enabled', '1') === '1') {
            $this->sendEmailNotification($alert);
        }

        if (Setting::getValue('alert_telegram_enabled', '0') === '1') {
            $this->sendTelegramNotification($alert);
        }
    }

    public function sendRecoveryNotification(Alert $alert): void
    {
        $alert->load('device');

        $this->sendDeviceRecoveryNotification($alert->device);
    }

    public function sendDeviceRecoveryNotification(Device $device): void
    {
        try {
            $message = "✅ RECOVERY: {$device->name} is back online";

            if (Setting::getValue('alert_email_enabled', '1') === '1') {
                $this->sendEmail(
                    "Recovery: {$device->name}",
                    $message
                );
            }

            if (Setting::getValue('alert_telegram_enabled', '0') === '1') {
                $this->sendTelegram($message);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send recovery notification', [
                'device_id' => $device->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendEmailNotification(Alert $alert): void
    {
        try {
            $this->sendEmail(
                '['.strtoupper($alert->severity)."] {$alert->device->name}",
                $alert->message
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send email notification', [
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendTelegramNotification(Alert $alert): void
    {
        try {
            $severityEmoji = match ($alert->severity) {
                'critical' => '🔴',
                'warning' => '🟡',
                'info' => '🔵',
                default => '⚪',
            };

            $message = "{$severityEmoji} *NetMon Alert*\n\n";
            $message .= '*'.strtoupper($alert->severity)."*\n";
            $message .= "Device: {$alert->device->name}\n";
            $message .= "IP: {$alert->device->ip_address}\n";
            $message .= "Message: {$alert->message}\n";
            $message .= "Time: {$alert->triggered_at->format('Y-m-d H:i:s')}";

            $this->sendTelegram($message);
        } catch (\Throwable $e) {
            Log::error('Failed to send Telegram notification', [
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendEmail(string $subject, string $body): void
    {
        $host = Setting::getValue('mail_host');
        $port = Setting::getValue('mail_port');
        $encryption = Setting::getValue('mail_encryption');
        $username = Setting::getValue('mail_username');
        $password = Setting::getValue('mail_password');
        $fromAddress = Setting::getValue('mail_from_address');
        $fromName = Setting::getValue('mail_from_name', 'NetMon Enterprise');
        $toAddress = Setting::getValue('mail_to_address');

        if (! $host || ! $port || ! $username || ! $password || ! $fromAddress || ! $toAddress) {
            Log::warning('Email SMTP not configured');

            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) $port,
            'mail.mailers.smtp.encryption' => $encryption,
            'mail.mailers.smtp.username' => $username,
            'mail.mailers.smtp.password' => $password,
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
        ]);

        Mail::raw($body, function ($message) use ($fromAddress, $fromName, $toAddress, $subject) {
            $message->to($toAddress)
                ->subject("[NetMon] {$subject}")
                ->from($fromAddress, $fromName);
        });
    }

    private function sendTelegram(string $message): void
    {
        $token = Setting::getValue('telegram_bot_token');
        $chatId = Setting::getValue('telegram_chat_id');

        if (! $token || ! $chatId) {
            Log::warning('Telegram not configured');

            return;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $response = Http::timeout(10)->post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);

        if (! $response->successful()) {
            Log::error('Telegram API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
