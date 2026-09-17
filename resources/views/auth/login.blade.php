<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - NetMon Enterprise</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-950 text-gray-100 antialiased flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="mb-4 flex justify-center">
                <img src="{{ asset('favicon.svg') }}" alt="NetMon" class="w-16 h-16">
            </div>
            <h1 class="text-2xl font-bold text-white">NetMon Enterprise</h1>
            <p class="text-gray-400 mt-1">Network Monitoring System</p>
        </div>

        <div class="bg-gray-900 rounded-xl p-8 border border-gray-800 shadow-xl">
            @if($errors->any())
                <div class="mb-4 bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 bg-gray-800 border-gray-700 rounded text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-400">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-gray-500 text-xs mt-6">NetMon Enterprise v1.0 MVP</p>
    </div>
</body>
</html>
