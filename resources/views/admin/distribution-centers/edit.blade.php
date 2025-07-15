@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Edit Distribution Center</h2>
    <form method="POST" action="{{ route('admin.distribution-centers.update', $center->id) }}" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Name</label>
                <input name="center_name" value="{{ old('center_name', $center->center_name) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Code</label>
                <input name="center_code" value="{{ old('center_code', $center->center_code) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Address</label>
                <input name="center_address" value="{{ old('center_address', $center->center_address) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Phone</label>
                <input name="center_phone" value="{{ old('center_phone', $center->center_phone) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input name="center_email" type="email" value="{{ old('center_email', $center->center_email) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Manager Name</label>
                <input name="center_manager" value="{{ old('center_manager', $center->center_manager) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Manager Phone</label>
                <input name="manager_phone" value="{{ old('manager_phone', $center->manager_phone) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Manager Email</label>
                <input name="manager_email" type="email" value="{{ old('manager_email', $center->manager_email) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Type</label>
                <select name="center_type" required class="border rounded px-3 py-2 w-full">
                    <option value="primary" @if($center->center_type=='primary') selected @endif>Primary</option>
                    <option value="secondary" @if($center->center_type=='secondary') selected @endif>Secondary</option>
                    <option value="regional" @if($center->center_type=='regional') selected @endif>Regional</option>
                    <option value="local" @if($center->center_type=='local') selected @endif>Local</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Storage Capacity (m³)</label>
                <input name="storage_capacity" type="number" value="{{ old('storage_capacity', $center->storage_capacity) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Current Inventory</label>
                <input name="current_inventory" type="number" value="{{ old('current_inventory', $center->current_inventory) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Temperature Control (°C)</label>
                <input name="temperature_control" type="number" step="0.01" value="{{ old('temperature_control', $center->temperature_control) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Humidity Control (%)</label>
                <input name="humidity_control" type="number" step="0.01" value="{{ old('humidity_control', $center->humidity_control) }}" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Delivery Vehicles</label>
                <input name="delivery_vehicles" type="number" value="{{ old('delivery_vehicles', $center->delivery_vehicles) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Delivery Radius (km)</label>
                <input name="delivery_radius" type="number" value="{{ old('delivery_radius', $center->delivery_radius) }}" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Facilities (comma separated)</label>
                <input name="facilities" value="{{ old('facilities', is_array($center->facilities) ? implode(',', $center->facilities) : (is_string($center->facilities) ? $center->facilities : '')) }}" class="border rounded px-3 py-2 w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Certifications (comma separated)</label>
                <input name="certifications" value="{{ old('certifications', is_array($center->certifications) ? implode(',', $center->certifications) : (is_string($center->certifications) ? $center->certifications : '')) }}" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Certification Status</label>
                <select name="certification_status" required class="border rounded px-3 py-2 w-full">
                    <option value="certified" @if($center->certification_status=='certified') selected @endif>Certified</option>
                    <option value="pending" @if($center->certification_status=='pending') selected @endif>Pending</option>
                    <option value="expired" @if($center->certification_status=='expired') selected @endif>Expired</option>
                    <option value="suspended" @if($center->certification_status=='suspended') selected @endif>Suspended</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Last Inspection Date</label>
                <input name="last_inspection_date" type="date" value="{{ old('last_inspection_date', $center->last_inspection_date) }}" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Next Inspection Date</label>
                <input name="next_inspection_date" type="date" value="{{ old('next_inspection_date', $center->next_inspection_date) }}" class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block font-semibold mb-1">Status</label>
                <select name="status" required class="border rounded px-3 py-2 w-full">
                    <option value="operational" @if($center->status=='operational') selected @endif>Operational</option>
                    <option value="maintenance" @if($center->status=='maintenance') selected @endif>Maintenance</option>
                    <option value="shutdown" @if($center->status=='shutdown') selected @endif>Shutdown</option>
                    <option value="suspended" @if($center->status=='suspended') selected @endif>Suspended</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Notes</label>
                <textarea name="notes" class="border rounded px-3 py-2 w-full">{{ old('notes', $center->notes) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">Assign Vendors</label>
                <select name="vendors[]" id="vendors" class="border rounded px-3 py-2 w-full" multiple>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @if($center->vendors->contains($vendor->id)) selected @endif>
                            {{ $vendor->company_name ?? ($vendor->user->name ?? 'Vendor #'.$vendor->id) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-6 flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Update</button>
            <a href="{{ route('admin.distribution-centers.index') }}" class="text-gray-600 hover:underline px-6 py-2">Cancel</a>
        </div>
    </form>
</div>
@endsection 