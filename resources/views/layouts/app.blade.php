<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Caramel Yogurt - Supply Chain Management Dashboard">
        <meta name="keywords" content="Yogurt, Supply Chain, Dashboard, Caramel Yogurt">
        <meta name="author" content="Caramel Yogurt Team">

        <title>{{ config('app.name', 'Caramel Yogurt') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <!-- Navigation Bar -->
        @include('layouts.navigation')
        @auth
            @include('components.sidebar')
            <div class="sidebar-overlay"></div>
        @endauth

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow flex items-center justify-between">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-blue-900 text-white mt-8">
            <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center">
                
                <div class="text-sm mb-2 md:mb-0">
                    &copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="hover:underline hover:text-blue-200">Privacy Policy</a>
                    <a href="#" class="hover:underline hover:text-blue-200">Terms of Service</a>
                    <a href="#" class="hover:underline hover:text-blue-200">Contact</a>
                </div>
            </div>
        </footer>

        <!-- Custom JavaScript -->
        <script src="{{ asset('js/carousel.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
