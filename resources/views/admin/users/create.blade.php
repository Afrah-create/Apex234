@extends('layouts.app')

@section('content')
<main class="main-content flex justify-center items-center">
    <div class="max-w-xl w-full bg-white p-8 rounded-lg shadow mt-10">
        <h2 class="text-2xl font-bold mb-6 text-center">Add New User</h2>
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block font-semibold mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="mobile" class="block font-semibold mb-1">Mobile</label>
                <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600" />
                <label for="is_active" class="ml-2 font-semibold">Active</label>
            </div>
            <div>
                <label for="photo_url" class="block font-semibold mb-1">Photo URL</label>
                <input type="url" id="photo_url" name="photo_url" value="{{ old('photo_url') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="role" class="block font-semibold mb-1">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="retailer" {{ old('role') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div id="driver-fields" style="display:none;">
                <div>
                    <label for="license" class="block font-semibold mb-1">Driver License</label>
                    <input type="text" name="license" id="license" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="license_expiry" class="block font-semibold mb-1">License Expiry Date</label>
                    <input type="date" name="license_expiry" id="license_expiry" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="vehicle_number" class="block font-semibold mb-1">Vehicle Number</label>
                    <input type="text" name="vehicle_number" id="vehicle_number" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="driver_photo" class="block font-semibold mb-1">Driver Photo</label>
                    <input type="file" name="driver_photo" id="driver_photo" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const roleSelect = document.getElementById('role');
                    const driverFields = document.getElementById('driver-fields');
                    if (roleSelect) {
                        roleSelect.addEventListener('change', function() {
                            if (this.value === 'driver') {
                                driverFields.style.display = '';
                            } else {
                                driverFields.style.display = 'none';
                            }
                        });
                        // Initial check
                        if (roleSelect.value === 'driver') {
                            driverFields.style.display = '';
                        }
                    }
                });
            </script>
            <div>
                <label for="password" class="block font-semibold mb-1">Password</label>
                <input type="password" id="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex justify-between items-center mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</main>
@endsection 