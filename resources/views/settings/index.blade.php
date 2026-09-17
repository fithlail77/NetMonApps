@extends('layouts.app')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">Settings</h2>
    </div>

    @if(session('success'))
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        {{-- Monitoring Settings --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 mb-6">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold">Monitoring</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Polling Interval (seconds)</label>
                    <input type="number" name="monitoring_interval" value="{{ $settings->get('monitoring_interval')->value ?? 60 }}" min="10" max="3600"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Ping Timeout (seconds)</label>
                    <input type="number" name="ping_timeout" value="{{ $settings->get('ping_timeout')->value ?? 5 }}" min="1" max="60"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">SNMP Timeout (seconds)</label>
                    <input type="number" name="snmp_timeout" value="{{ $settings->get('snmp_timeout')->value ?? 10 }}" min="1" max="60"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Email Settings --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 mb-6">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold">Email Notifications</h3>
            </div>
            <div class="p-6 space-y-4">
                <label class="flex items-center">
                    <input type="checkbox" name="alert_email_enabled" value="1"
                           {{ ($settings->get('alert_email_enabled')->value ?? '') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 bg-gray-800 border-gray-700 rounded text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-300">Enable email notifications for alerts</span>
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">SMTP Host</label>
                        <input type="text" name="mail_host" id="mail_host"
                               value="{{ $settings->get('mail_host')->value ?? 'smtp.gmail.com' }}"
                               placeholder="smtp.gmail.com"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">SMTP Port</label>
                        <input type="text" name="mail_port" id="mail_port"
                               value="{{ $settings->get('mail_port')->value ?? '587' }}"
                               placeholder="587"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Encryption</label>
                    <select name="mail_encryption" id="mail_encryption"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="tls" {{ ($settings->get('mail_encryption')->value ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings->get('mail_encryption')->value ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="" {{ ($settings->get('mail_encryption')->value ?? '') == '' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">SMTP Username (Email)</label>
                    <input type="text" name="mail_username" id="mail_username"
                           value="{{ $settings->get('mail_username')->value ?? '' }}"
                           placeholder="your-email@gmail.com"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">SMTP Password</label>
                    <input type="password" name="mail_password" id="mail_password"
                           value="{{ $settings->get('mail_password')->value ?? '' }}"
                           placeholder="App password or SMTP password"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">From Address</label>
                        <input type="email" name="mail_from_address" id="mail_from_address"
                               value="{{ $settings->get('mail_from_address')->value ?? '' }}"
                               placeholder="netmon@yourdomain.com"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">From Name</label>
                        <input type="text" name="mail_from_name" id="mail_from_name"
                               value="{{ $settings->get('mail_from_name')->value ?? 'NetMon Enterprise' }}"
                               placeholder="NetMon Enterprise"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Recipient Email (notifications will be sent here)</label>
                    <input type="email" name="mail_to_address" id="mail_to_address"
                           value="{{ $settings->get('mail_to_address')->value ?? '' }}"
                           placeholder="admin@yourdomain.com"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <p class="text-xs text-gray-500">For Gmail, use App Password (not regular password). Generate at myaccount.google.com/apppasswords.</p>

                <div class="pt-2">
                    <button type="button" onclick="testEmail()" class="text-sm bg-gray-800 hover:bg-gray-700 text-blue-400 hover:text-blue-300 px-4 py-2 rounded-lg border border-gray-700 transition">
                        Send Test Email
                    </button>
                </div>
            </div>
        </div>

        {{-- Telegram Settings --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 mb-6">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold">Telegram Notifications</h3>
            </div>
            <div class="p-6 space-y-4">
                <label class="flex items-center">
                    <input type="checkbox" name="alert_telegram_enabled" value="1"
                           {{ ($settings->get('alert_telegram_enabled')->value ?? '') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 bg-gray-800 border-gray-700 rounded text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-300">Enable Telegram notifications for alerts</span>
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Bot Token</label>
                    <input type="text" name="telegram_bot_token" id="telegram_bot_token"
                           value="{{ $settings->get('telegram_bot_token')->value ?? '' }}"
                           placeholder="123456:ABC-DEF..."
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Chat ID</label>
                    <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                           value="{{ $settings->get('telegram_chat_id')->value ?? '' }}"
                           placeholder="-1001234567890"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <p class="text-xs text-gray-500">Get your Chat ID by sending a message to @userinfobot on Telegram.</p>

                <div class="pt-2">
                    <button type="button" onclick="testTelegram()" class="text-sm bg-gray-800 hover:bg-gray-700 text-blue-400 hover:text-blue-300 px-4 py-2 rounded-lg border border-gray-700 transition">
                        Send Test Message
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                Save Settings
            </button>
        </div>
    </form>
</div>

<script>
function testEmail() {
    const host = document.getElementById('mail_host').value;
    const port = document.getElementById('mail_port').value;
    const username = document.getElementById('mail_username').value;
    const password = document.getElementById('mail_password').value;
    const fromAddress = document.getElementById('mail_from_address').value;
    const toAddress = document.getElementById('mail_to_address').value;

    if (!host || !port || !username || !password || !fromAddress || !toAddress) {
        alert('Please fill in all SMTP fields including Recipient Email.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("settings.email.test") }}';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    const fields = {
        'mail_host': host,
        'mail_port': port,
        'mail_encryption': document.getElementById('mail_encryption').value,
        'mail_username': username,
        'mail_password': password,
        'mail_from_address': fromAddress,
        'mail_from_name': document.getElementById('mail_from_name').value,
        'mail_to_address': toAddress
    };

    for (const [name, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
}

function testTelegram() {
    const token = document.getElementById('telegram_bot_token').value;
    const chatId = document.getElementById('telegram_chat_id').value;

    if (!token || !chatId) {
        alert('Please fill in Bot Token and Chat ID first.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("settings.telegram.test") }}';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = 'telegram_bot_token';
    tokenInput.value = token;
    form.appendChild(tokenInput);

    const chatInput = document.createElement('input');
    chatInput.type = 'hidden';
    chatInput.name = 'telegram_chat_id';
    chatInput.value = chatId;
    form.appendChild(chatInput);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
