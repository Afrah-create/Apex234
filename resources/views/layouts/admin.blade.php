<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Caramel Yogurt Admin') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="window.sidebarOpenStore || (window.sidebarOpenStore = { sidebarOpen: Alpine.store('sidebarOpen', { open: false }) })" class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="{'block': $store.sidebarOpen.open, 'hidden': !$store.sidebarOpen.open, 'absolute inset-y-0 left-0 z-40': $store.sidebarOpen.open, 'md:static md:block': true}" class="w-64 bg-gray-900 text-white flex-shrink-0 p-6 space-y-6 hidden md:block transition-all duration-200 overflow-y-auto h-full">
            <div class="mb-8">
                <div class="text-2xl font-bold mb-2">Admin Panel</div>
                <div class="text-sm text-gray-300">Administrator</div>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="{{ route('dashboard') }}" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h2a2 2 0 002-2v-7a2 2 0 00-.586-1.414l-8-8a2 2 0 00-2.828 0l-8 8A2 2 0 003 10v7a2 2 0 002 2h2"/></svg>
                    Dashboard Home
                </a>
                <a href="{{ route('admin.users.index') }}" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Manage Users
                </a>
                <!-- Add more sidebar links as needed -->
            </nav>
            <div class="mt-8">
                <div class="text-xs text-gray-400 uppercase mb-2">Roles</div>
                <ul class="text-sm space-y-1">
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2z"/></svg>Admin: Full Access</li>
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Can manage users, roles, and all supply chain data</li>
                </ul>
            </div>
        </aside>
        <!-- Overlay for mobile -->
        <div x-show="$store.sidebarOpen.open" @click="$store.sidebarOpen.open = false" class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden" x-cloak></div>
        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50 w-full">
            <div class="flex justify-end mb-4">
                <a href="{{ route('profile.edit') }}" class="relative group mr-2">
                    <svg class="w-7 h-7 text-gray-600 hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="sr-only">Profile</span>
                </a>
            </div>
            {{ $slot }}
        </main>
    </div>
    <footer class="bg-blue-900 text-white mt-8">
        <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center">
            <div class="text-sm mb-2 md:mb-0">
                &copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('privacy.policy') }}" class="hover:underline hover:text-blue-200">Privacy Policy</a>
                <a href="{{ route('terms.use') }}" class="hover:underline hover:text-blue-200">Terms of Service</a>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/carousel.js') }}"></script>
</body>
</html> 