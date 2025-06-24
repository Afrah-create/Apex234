<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f4f4f4; }
        .sidebar {
            min-width: 220px;
            max-width: 220px;
            background: #6c63ff;
            color: #fff;
            min-height: 100vh;
        }
        .sidebar a { color: #fff; display: block; padding: 12px 20px; text-decoration: none; }
        .sidebar a:hover, .sidebar .active { background: #554fd8; }
        .sidebar .sidebar-header { font-size: 1.3rem; font-weight: bold; padding: 20px; }
        .topbar { background: #fff; border-bottom: 1px solid #eee; padding: 10px 30px; }
        .admin-info { float: right; }
        .main-content { padding: 30px; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="sidebar-header">Norsk Timeregistrering</div>
        <a href="#">Profile</a>
        <a href="#" class="active">Users</a>
        <a href="#">Control panel</a>
        <a href="#">Projects</a>
        <a href="#">Tasks</a>
        <a href="#">Logs</a>
        <a href="#">Group chats</a>
        <a href="#">Reports</a>
    </div>
    <!-- Main Content -->
    <div class="flex-grow-1">
        <!-- Top Bar -->
        <div class="topbar clearfix">
            <form class="form-inline d-inline-block">
                <input class="form-control mr-sm-2" type="search" placeholder="Search..." aria-label="Search">
            </form>
            <div class="admin-info d-inline-block">
                <span>Luke Aoste</span> |
                <span class="text-muted">Admin for Associations</span>
                <img src="https://i.pravatar.cc/40" class="rounded-circle ml-2" alt="Admin Avatar">
            </div>
            <div class="flex justify-end mb-4">
                <a href="{{ route('notifications.index') }}" class="relative group mr-2">
                    <svg class="w-7 h-7 text-gray-600 hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="sr-only">Notifications</span>
                </a>
            </div>
        </div>
        <!-- Main Content Area -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html> 