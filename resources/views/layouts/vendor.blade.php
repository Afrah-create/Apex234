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
</body>
</html> 