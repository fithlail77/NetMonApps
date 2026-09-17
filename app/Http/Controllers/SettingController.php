<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'monitoring_interval' => 'required|integer|min:10|max:3600',
            'ping_timeout' => 'required|integer|min:1|max:60',
            'snmp_timeout' => 'required|integer|min:1|max:60',
            'alert_email_enabled' => 'nullable',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            'mail_to_address' => 'nullable|email',
            'alert_telegram_enabled' => 'nullable',
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
        ]);

        $groups = [
            'monitoring_interval' => 'monitoring',
            'ping_timeout' => 'monitoring',
            'snmp_timeout' => 'monitoring',
            'alert_email_enabled' => 'email',
            'mail_host' => 'email',
            'mail_port' => 'email',
            'mail_encryption' => 'email',
            'mail_username' => 'email',
            'mail_password' => 'email',
            'mail_from_address' => 'email',
            'mail_from_name' => 'email',
            'mail_to_address' => 'email',
            'alert_telegram_enabled' => 'telegram',
            'telegram_bot_token' => 'telegram',
            'telegram_chat_id' => 'telegram',
        ];

        foreach ($validated as $key => $value) {
            $group = $groups[$key] ?? 'general';
            Setting::setValue($key, $value, $group);
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $host = $request->input('mail_host') ?? Setting::getValue('mail_host');
        $port = $request->input('mail_port') ?? Setting::getValue('mail_port');
        $encryption = $request->input('mail_encryption') ?? Setting::getValue('mail_encryption');
        $username = $request->input('mail_username') ?? Setting::getValue('mail_username');
        $password = $request->input('mail_password') ?? Setting::getValue('mail_password');
        $fromAddress = $request->input('mail_from_address') ?? Setting::getValue('mail_from_address');
        $fromName = $request->input('mail_from_name') ?? Setting::getValue('mail_from_name', 'NetMon Enterprise');
        $toAddress = $request->input('mail_to_address') ?? Setting::getValue('mail_to_address');

        if (! $host || ! $port || ! $username || ! $password || ! $fromAddress || ! $toAddress) {
            return redirect()->back()->with('error', 'Please fill in all SMTP fields including recipient email before testing.');
        }

        try {
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

            $body = "This is a test notification from NetMon Enterprise.\n\nTime: " . now()->format('Y-m-d H:i:s');

            Mail::raw($body, function ($message) use ($fromAddress, $fromName, $toAddress) {
                $message->to($toAddress)
                    ->subject('[NetMon] Test Email Notification')
                    ->from($fromAddress, $fromName);
            });

            return redirect()->back()->with('success', 'Test email sent successfully to ' . $toAddress);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function testTelegram(Request $request)
    {
        $token = $request->input('telegram_bot_token') ?? Setting::getValue('telegram_bot_token');
        $chatId = $request->input('telegram_chat_id') ?? Setting::getValue('telegram_chat_id');

        if (! $token || ! $chatId) {
            return redirect()->back()->with('error', 'Telegram bot token and chat ID are required. Save settings first or fill in the fields.');
        }

        $message = "NetMon Enterprise Test\n\nThis is a test notification from NetMon Enterprise.\n\nTime: " . now()->format('Y-m-d H:i:s');

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        try {
            $response = Http::withoutVerifying()->timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
            ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Telegram test message sent successfully.');
            }

            $error = $response->json('description') ?? $response->body();
            return redirect()->back()->with('error', 'Telegram API error: ' . $error);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to connect to Telegram: ' . $e->getMessage());
        }
    }
}
