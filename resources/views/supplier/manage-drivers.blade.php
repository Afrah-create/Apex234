@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Manage Drivers</h2>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
        @endif
        <div class="mb-6">
            <h3 class="font-semibold mb-2">Add New Driver</h3>
            <form method="POST" action="{{ route('supplier.drivers.store') }}" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input type="text" name="name" class="w-full border rounded px-3 py-2" placeholder="Name" required @if($drivers->count() >= 3) disabled @endif value="{{ old('name') }}">
                        @error('name')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="text" name="phone" class="w-full border rounded px-3 py-2" placeholder="Phone" required @if($drivers->count() >= 3) disabled @endif value="{{ old('phone') }}">
                        @error('phone')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="email" name="email" class="w-full border rounded px-3 py-2" placeholder="Email" @if($drivers->count() >= 3) disabled @endif value="{{ old('email') }}">
                        @error('email')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="text" name="address" class="w-full border rounded px-3 py-2" placeholder="Address" @if($drivers->count() >= 3) disabled @endif value="{{ old('address') }}">
                        @error('address')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="date" name="date_of_birth" class="w-full border rounded px-3 py-2" placeholder="Date of Birth" @if($drivers->count() >= 3) disabled @endif value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="text" name="license" class="w-full border rounded px-3 py-2" placeholder="License" required @if($drivers->count() >= 3) disabled @endif value="{{ old('license') }}">
                        @error('license')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="date" name="license_expiry" class="w-full border rounded px-3 py-2" placeholder="License Expiry" @if($drivers->count() >= 3) disabled @endif value="{{ old('license_expiry') }}">
                        @error('license_expiry')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="text" name="emergency_contact" class="w-full border rounded px-3 py-2" placeholder="Emergency Contact" @if($drivers->count() >= 3) disabled @endif value="{{ old('emergency_contact') }}">
                        @error('emergency_contact')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="file" name="photo" class="w-full border rounded px-3 py-2" accept="image/*" @if($drivers->count() >= 3) disabled @endif>
                        @error('photo')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <input type="text" name="vehicle_number" class="w-full border rounded px-3 py-2" placeholder="Vehicle Number Plate" @if($drivers->count() >= 3) disabled @endif value="{{ old('vehicle_number') }}">
                        @error('vehicle_number')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded" @if($drivers->count() >= 3) disabled @endif>Add Driver</button>
                </div>
                @if($drivers->count() >= 3)
                    <div class="text-red-600 text-xs mt-2">You can only have up to 3 drivers.</div>
                @endif
            </form>
        </div>
        <div>
            <h3 class="font-semibold mb-2">Your Drivers</h3>
            <div class="overflow-x-auto">
            <table class="min-w-full text-sm mb-2 whitespace-nowrap">
                <thead>
                    <tr class="bg-blue-50">
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Phone</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Address</th>
                        <th class="px-4 py-2 text-left">DOB</th>
                        <th class="px-4 py-2 text-left">License</th>
                        <th class="px-4 py-2 text-left">Expiry</th>
                        <th class="px-4 py-2 text-left">Emergency Contact</th>
                        <th class="px-4 py-2 text-left">Photo</th>
                        <th class="px-4 py-2 text-left">Vehicle Number</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($drivers as $driver)
                    <tr>
                        <form method="POST" action="{{ route('supplier.drivers.update', $driver->id) }}" class="contents" enctype="multipart/form-data">
                            @csrf
                            <td class="px-4 py-2 min-w-[120px]">
                                <input type="text" name="name" value="{{ $driver->name }}" class="border rounded px-2 py-1 w-full" required>
                            </td>
                            <td class="px-4 py-2 min-w-[120px]">
                                <input type="text" name="phone" value="{{ $driver->phone }}" class="border rounded px-2 py-1 w-full" required>
                            </td>
                            <td class="px-4 py-2 min-w-[160px]">
                                <input type="email" name="email" value="{{ $driver->email }}" class="border rounded px-2 py-1 w-full">
                            </td>
                            <td class="px-4 py-2 min-w-[160px]">
                                <input type="text" name="address" value="{{ $driver->address }}" class="border rounded px-2 py-1 w-full">
                            </td>
                            <td class="px-4 py-2 min-w-[120px]">
                                <input type="date" name="date_of_birth" value="{{ $driver->date_of_birth }}" class="border rounded px-2 py-1 w-full">
                            </td>
                            <td class="px-4 py-2 min-w-[100px]">
                                <input type="text" name="license" value="{{ $driver->license }}" class="border rounded px-2 py-1 w-full" required>
                            </td>
                            <td class="px-4 py-2 min-w-[120px]">
                                <input type="date" name="license_expiry" value="{{ $driver->license_expiry }}" class="border rounded px-2 py-1 w-full">
                            </td>
                            <td class="px-4 py-2 min-w-[160px]">
                                <input type="text" name="emergency_contact" value="{{ $driver->emergency_contact }}" class="border rounded px-2 py-1 w-full">
                            </td>
                            <td class="px-4 py-2 min-w-[100px] text-center">
                                @if($driver->photo)
                                    <img src="{{ asset('storage/' . $driver->photo) }}" alt="Driver Photo" class="w-10 h-10 rounded-full object-cover mx-auto">
                                @else
                                    <span class="text-gray-400">No photo</span>
                                @endif
                                <input type="file" name="photo" class="border rounded px-2 py-1 w-full mt-1" accept="image/*">
                            </td>
                            <td class="px-4 py-2 min-w-[140px]">
                                <input type="text" name="vehicle_number" value="{{ $driver->vehicle_number }}" class="border rounded px-2 py-1 w-full">
                            </td>
                            <td class="px-4 py-2 flex gap-2 min-w-[120px]">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">Update</button>
                        </form>
                        <form method="POST" action="{{ route('supplier.drivers.delete', $driver->id) }}" onsubmit="return confirm('Delete this driver?')">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Delete</button>
                        </form>
                            </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center py-4 text-gray-500">No drivers found.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection 