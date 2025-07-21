@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Reports & Analytics</h1>
        <p class="text-gray-500 text-lg">Access key business insights and analytics. Select a report below to get started.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 justify-center">
        <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center hover:shadow-2xl transition cursor-pointer">
            <div class="bg-blue-100 rounded-full p-4 mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Sales Report</h2>
            <p class="text-gray-500 mb-4 text-center">View total sales, sales by product, vendor, and trends over time.</p>
            <a href="{{ route('admin.reports.sales') }}" class="mt-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">View Sales Report</a>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center hover:shadow-2xl transition cursor-pointer">
            <div class="bg-green-100 rounded-full p-4 mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 17v-2a4 4 0 014-4h10a4 4 0 014 4v2M12 3v9m0 0l-3-3m3 3l3-3" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Production Report</h2>
            <p class="text-gray-500 mb-4 text-center">Analyze production batches, units produced, and efficiency trends.</p>
            <a href="{{ route('admin.reports.production') }}" class="mt-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">View Production Report</a>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center hover:shadow-2xl transition cursor-pointer">
            <div class="bg-blue-100 rounded-full p-4 mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M3 7h18M3 11h18M3 15h18M3 19h18" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Inventory Report</h2>
            <p class="text-gray-500 mb-4 text-center">Monitor current stock, low stock alerts, and inventory by product and center.</p>
            <a href="{{ route('admin.reports.inventory') }}" class="mt-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">View Inventory Report</a>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center hover:shadow-2xl transition cursor-pointer">
            <div class="bg-purple-100 rounded-full p-4 mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 10c-4.41 0-8-1.79-8-4V6c0-2.21 3.59-4 8-4s8 1.79 8 4v8c0 2.21-3.59 4-8 4z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Raw Material Orders Report</h2>
            <p class="text-gray-500 mb-4 text-center">Track raw material orders, supplier performance, and procurement spend.</p>
            <a href="{{ route('admin.reports.raw_material_orders') }}" class="mt-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">View Raw Material Orders Report</a>
        </div>
        <div class="bg-yellow-100 rounded-xl shadow-lg p-8 flex flex-col items-center hover:shadow-2xl transition cursor-pointer">
            <div class="bg-yellow-200 rounded-full p-4 mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Scheduled Reports</h2>
            <p class="text-gray-500 mb-4 text-center">View and manage scheduled reports and their delivery.</p>
            <a href="{{ route('admin.reports.scheduled') }}" class="mt-auto bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg font-semibold transition">View Scheduled Reports</a>
        </div>
        <!-- Add more report cards here in the future -->
    </div>
</div>
@endsection 