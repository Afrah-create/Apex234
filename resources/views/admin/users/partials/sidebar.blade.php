@php
    // Accept a $role variable, default to 'admin' if not provided
    $role = $role ?? (auth()->user()->roles->first()->name ?? 'admin');
@endphp
<aside :class="{'block': $store.sidebarOpen.open, 'hidden': !$store.sidebarOpen.open, 'absolute inset-y-0 left-0 z-40': $store.sidebarOpen.open, 'md:static md:block': true}" class="sidebar">
    <div>
        @if($role === 'admin')
            <div class="font-bold text-lg mb-2">Management Console</div>
            <div class="text-sm text-gray-300 mb-4">System Administrator</div>
        @elseif($role === 'vendor')
            <div class="font-bold text-lg mb-2">Vendor Panel</div>
            <div class="text-sm text-gray-300 mb-4">Vendor</div>
        @elseif($role === 'retailer')
            <div class="font-bold text-lg mb-2">Retailer Panel</div>
            <div class="text-sm text-gray-300 mb-4">Retailer</div>
        @elseif($role === 'supplier')
            <div class="font-bold text-lg mb-2">Supplier Panel</div>
            <div class="text-sm text-gray-300 mb-4">Supplier</div>
        @endif
    </div>
    <nav class="flex flex-col gap-2">
        @if($role === 'admin')
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-tachometer-alt'></i> --></span>
                <span>Executive Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-users'></i> --></span>
                <span>User Management</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-box'></i> --></span>
                <span>Order Management</span>
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-warehouse'></i> --></span>
                <span>Inventory Analytics</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-user-cog'></i> --></span>
                <span>Profile</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-chart-bar'></i> --></span>
                <span>Analytics & Reports</span>
            </a>
        @elseif($role === 'vendor')
            <a href="{{ route('dashboard.vendor') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-home'></i> --></span>
                <span>Dashboard Home</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-box'></i> --></span>
                <span>Manage Orders</span>
            </a>
        @elseif($role === 'retailer')
            <a href="{{ route('dashboard.retailer') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-home'></i> --></span>
                <span>Dashboard Home</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-boxes'></i> --></span>
                <span>View Stock</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-chart-line'></i> --></span>
                <span>Sales Reports</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-shopping-cart'></i> --></span>
                <span>Order Products</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-truck'></i> --></span>
                <span>Deliveries</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-user'></i> --></span>
                <span>Profile</span>
            </a>
        @elseif($role === 'supplier')
            <a href="{{ route('dashboard.supplier') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-home'></i> --></span>
                <span>Dashboard Home</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg transition bg-opacity-0 hover:bg-blue-900/80 hover:bg-opacity-80 focus:bg-blue-900/90 sidebar-link">
                <span class="sidebar-icon"><!-- <i class='fas fa-boxes'></i> --></span>
                <span>Manage Supplies</span>
            </a>
        @endif
    </nav>
</aside> 