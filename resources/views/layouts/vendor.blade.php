<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-800 text-white p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('dashboard.vendor') }}" class="font-bold text-xl">Vendor Dashboard</a>
            <div>
                <a href="{{ route('vendor.production.index') }}" class="ml-4 hover:underline">Production</a>
                <!-- Add other vendor links here -->
            </div>
        </div>
    </nav>
    <main>
        @yield('content')
    </main>
    <footer class="bg-blue-900 text-white mt-8">
        <div class="container mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center">
            <div class="text-sm mb-2 md:mb-0">
                &copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('privacy.policy') }}" class="hover:underline hover:text-blue-200">Privacy Policy</a>
                <a href="{{ route('terms.use') }}" class="hover:underline hover:text-blue-200">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html> 