<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'NetMon Enterprise' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('head_scripts')
</head>
<body class="bg-gray-950 text-gray-100 antialiased">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-gray-900 border-r border-gray-800 transition-all duration-300 flex flex-col flex-shrink-0">
            {{-- Logo --}}
            <div class="h-16 flex items-center px-4 border-b border-gray-800">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span x-show="sidebarOpen" x-transition class="font-bold text-lg text-white whitespace-nowrap">NetMon</span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-4 overflow-y-auto">
                <ul class="space-y-1 px-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('devices.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('devices.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>Devices</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('topology.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('topology.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>Topology</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('alerts.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('alerts.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>Alerts</span>
                            @if(\App\Models\Alert::active()->count() > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ \App\Models\Alert::active()->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('metrics.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('metrics.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>Metrics</span>
                        </a>
                    </li>
                    @if(in_array(auth()->user()->role, ['admin', 'it_manager']))
                    <li>
                        <a href="{{ route('sla.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('sla.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>SLA Reports</span>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>Users</span>
                        </a>
                    </li>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'it_manager']))
                    <li>
                        <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" x-transition ml-3>Settings</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>

            {{-- User Info --}}
            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <div x-show="sidebarOpen" x-transition class="ml-3">
                        <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto">
            {{-- Header --}}
            <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold">{{ $header ?? '' }}</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-400" x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleString(), 1000)" x-text="time"></span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white text-sm">Logout</button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
