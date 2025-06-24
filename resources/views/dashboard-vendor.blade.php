<x-app-layout>
    <div x-data="window.sidebarOpenStore || (window.sidebarOpenStore = { sidebarOpen: Alpine.store('sidebarOpen', { open: false }) })" class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="{'block': $store.sidebarOpen.open, 'hidden': !$store.sidebarOpen.open, 'absolute inset-y-0 left-0 z-40': $store.sidebarOpen.open, 'md:static md:block': true}" class="w-64 bg-gray-900 text-white flex-shrink-0 p-6 space-y-6 hidden md:block transition-all duration-200 overflow-y-auto h-full">
            <div class="mb-8">
                <div class="text-2xl font-bold mb-2">Vendor Panel</div>
                <div class="text-sm text-gray-300">Vendor</div>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="{{ route('dashboard.vendor') }}" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h2a2 2 0 002-2v-7a2 2 0 00-.586-1.414l-8-8a2 2 0 00-2.828 0l-8 8A2 2 0 003 10v7a2 2 0 002 2h2"/></svg>
                    Dashboard Home
                </a>
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Manage Orders
                </a>
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    Transactions
                </a>
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Reports
                </a>
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Profile
                </a>
            </nav>
            <div class="mt-8">
                <div class="text-xs text-gray-400 uppercase mb-2">Roles</div>
                <ul class="text-sm space-y-1">
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2z"/></svg>Vendor: Manage orders and transactions</li>
                    <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>View reports</li>
                </ul>
            </div>
        </aside>
        <!-- Overlay for mobile -->
        <div x-show="$store.sidebarOpen.open" @click="$store.sidebarOpen.open = false" class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden" x-cloak></div>
        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50 w-full">
            <h1 class="text-2xl font-bold mb-6">Vendor Dashboard</h1>
            <p class="mb-4">Welcome, Vendor! Use the sidebar to manage your orders and transactions.</p>
        </main>
    </div>
</x-app-layout> 