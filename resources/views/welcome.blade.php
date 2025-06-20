<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caramel Yoghurt Supply Chain Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md mx-auto bg-white dark:bg-[#161615] rounded-lg shadow-md p-8 text-center">
        <h1 class="text-3xl font-bold mb-4">Welcome to Caramel Yoghurt Supply Chain Management System</h1>
        <p class="mb-8 text-gray-600 dark:text-gray-300">Efficiently manage your supply chain as an Administrator, Supplier, Retailer, or Vendor.</p>
        <div class="flex flex-col gap-4">
            <a href="{{ route('login') }}" class="w-full inline-block px-6 py-3 bg-[#F53003] text-white font-semibold rounded hover:bg-[#c42a00] transition">Login</a>
            <a href="{{ route('register') }}" class="w-full inline-block px-6 py-3 bg-[#F8B803] text-[#1b1b18] font-semibold rounded hover:bg-[#e0a800] transition">Register</a>
        </div>
    </div>
</body>
</html>
