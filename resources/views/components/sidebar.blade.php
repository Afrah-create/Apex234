@auth
    @php
        $role = auth()->user()->roles->first()->name ?? 'admin';
    @endphp
    <nav class="sidebar-horizontal w-full bg-blue-900 text-white flex flex-col md:flex-row md:items-center gap-2 md:gap-6 px-6 py-3 shadow relative">
        @if($role === 'admin')
            <div class="sidebar-meta mb-2 md:mb-0 md:mr-6 flex flex-col md:flex-col items-start">
                <span class="sidebar-meta-title">Management Console</span>
                <span class="sidebar-meta-subtitle">System Administrator</span>
            </div>
        @endif
        @if($role === 'vendor')
            <div class="sidebar-meta mb-2 md:mb-0 md:mr-6 flex flex-col md:flex-col items-start">
                <span class="sidebar-meta-title">Vendor Panel</span>
                <span class="sidebar-meta-subtitle">Product &amp; Order Management</span>
            </div>
        @endif
        @if($role === 'supplier')
            <div class="sidebar-meta mb-2 md:mb-0 md:mr-6 flex flex-col md:flex-col items-start">
                <span class="sidebar-meta-title">Supplier Panel</span>
                <span class="sidebar-meta-subtitle">Supply &amp; Delivery Management</span>
            </div>
        @endif
        @if($role === 'retailer')
            <div class="sidebar-meta mb-2 md:mb-0 md:mr-6 flex flex-col md:flex-col items-start">
                <span class="sidebar-meta-title">Retailer Panel</span>
                <span class="sidebar-meta-subtitle">Stock &amp; Sales Management</span>
            </div>
        @endif
        <div id="sidebar-links" class="flex flex-row md:flex-row items-center gap-2 md:gap-6 w-full md:w-auto sidebar-links">
            @if($role === 'admin')
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h2a2 2 0 002-2v-7a2 2 0 00-.586-1.414l-8-8a2 2 0 00-2.828 0l-8 8A2 2 0 003 10v7a2 2 0 002 2h2"/></svg>
                    Executive Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    User Management
                </a>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg>
                    Order Management
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Inventory Analytics
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2"/></svg>
                    Analytics & Reports
                </a>
            @elseif($role === 'vendor')
                <a href="{{ route('dashboard.vendor') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h2a2 2 0 002-2v-7a2 2 0 00-.586-1.414l-8-8a2 2 0 00-2.828 0l-8 8A2 2 0 003 10v7a2 2 0 002 2h2"/></svg>
                    Dashboard Home
                </a>
                <a href="{{ route('vendor.manage-orders') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg>
                    Manage Orders
                </a>
                <a href="{{ route('vendor.manage-products') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Manage Products
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2"/></svg>
                    View Transactions
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2"/></svg>
                    Analytics
                </a>
            @elseif($role === 'retailer')
                <a href="{{ route('dashboard.retailer') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h2a2 2 0 002-2v-7a2 2 0 00-.586-1.414l-8-8a2 2 0 00-2.828 0l-8 8A2 2 0 003 10v7a2 2 0 002 2h2"/></svg>
                    Dashboard Home
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    View Stock
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2"/></svg>
                    Sales Reports
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v6H3V3zm0 8h18v10H3V11z"/></svg>
                    Order Products
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2"/></svg>
                    Deliveries
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Profile
                </a>
            @elseif($role === 'supplier')
                <a href="{{ route('dashboard.supplier') }}" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h2a2 2 0 002-2v-7a2 2 0 00-.586-1.414l-8-8a2 2 0 00-2.828 0l-8 8A2 2 0 003 10v7a2 2 0 002 2h2"/></svg>
                    Dashboard Home
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Manage Supplies
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Track Deliveries
                </a>
                <a href="#" class="sidebar-link flex items-center gap-2 px-4 py-2 rounded-lg transition hover:bg-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Raw Material Management
                </a>
            @endif
        </div>
        <button class="sidebar-menu-btn md:hidden block absolute left-2 top-1/2 -translate-y-1/2" aria-label="Toggle menu" onclick="document.getElementById('sidebar-links').classList.toggle('open')">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </nav>
    <script>
        // Hide sidebar links by default on small screens
        document.addEventListener('DOMContentLoaded', function() {
            var links = document.getElementById('sidebar-links');
            function updateSidebarLinks() {
                if(window.innerWidth <= 900) {
                    links.classList.remove('open');
                } else {
                    links.classList.add('open');
                }
            }
            updateSidebarLinks();
            window.addEventListener('resize', updateSidebarLinks);
        });
    </script>
@endauth 