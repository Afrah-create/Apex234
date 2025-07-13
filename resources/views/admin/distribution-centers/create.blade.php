@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Add Distribution Center</h2>
    <form method="POST" action="{{ route('admin.distribution-centers.store') }}" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Name</label>
                <input name="center_name" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Code</label>
                <input name="center_code" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Address</label>
                <input name="center_address" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Phone</label>
                <input name="center_phone" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input name="center_email" type="email" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Manager Name</label>
                <input name="center_manager" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Manager Phone</label>
                <input name="manager_phone" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Manager Email</label>
                <input name="manager_email" type="email" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Type</label>
                <select name="center_type" required class="border rounded px-3 py-2 w-full">
                    <option value="primary">Primary</option>
                    <option value="secondary">Secondary</option>
                    <option value="regional">Regional</option>
                    <option value="local">Local</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Storage Capacity (m³)</label>
                <input name="storage_capacity" type="number" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Current Inventory</label>
                <input name="current_inventory" type="number" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Temperature Control (°C)</label>
                <input name="temperature_control" type="number" step="0.01" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Humidity Control (%)</label>
                <input name="humidity_control" type="number" step="0.01" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Delivery Vehicles</label>
                <input name="delivery_vehicles" type="number" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Delivery Radius (km)</label>
                <input name="delivery_radius" type="number" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Facilities (comma separated)</label>
                <input name="facilities" class="border rounded px-3 py-2 w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Certifications (comma separated)</label>
                <input name="certifications" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Certification Status</label>
                <select name="certification_status" required class="border rounded px-3 py-2 w-full">
                    <option value="certified">Certified</option>
                    <option value="pending">Pending</option>
                    <option value="expired">Expired</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Last Inspection Date</label>
                <input name="last_inspection_date" type="date" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Next Inspection Date</label>
                <input name="next_inspection_date" type="date" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Status</label>
                <select name="status" required class="border rounded px-3 py-2 w-full">
                    <option value="operational">Operational</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="shutdown">Shutdown</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Notes</label>
                <textarea name="notes" class="border rounded px-3 py-2 w-full"></textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Create</button>
            <a href="{{ route('admin.distribution-centers.index') }}" class="text-gray-600 hover:underline px-6 py-2">Cancel</a>
        </div>
    </form>
</div>
@endsection 