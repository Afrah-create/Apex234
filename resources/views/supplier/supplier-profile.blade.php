@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <div class="bg-white rounded-lg shadow p-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-blue-900">Supplier Profile & Farm Info</h1>
            <a href="{{ route('supplier.raw-material-inventory') }}" class="text-blue-600 hover:underline">&larr; Back to Inventory</a>
        </div>
        @if(session('success'))
            <div class="mb-4 text-green-700 bg-green-100 rounded p-3">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 text-red-700 bg-red-100 rounded p-3">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('supplier.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="border-b pb-4 mb-4">
                <h2 class="text-lg font-semibold text-blue-800 mb-2">Supplier Information</h2>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Company Name</label>
                    <input type="text" name="company_name" class="flex-1 border rounded px-3 py-2" required value="{{ old('company_name', $supplier->company_name ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Registration Number</label>
                    <input type="text" name="registration_number" class="flex-1 border rounded px-3 py-2" required value="{{ old('registration_number', $supplier->registration_number ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Business Address</label>
                    <input type="text" name="business_address" class="flex-1 border rounded px-3 py-2" required value="{{ old('business_address', $supplier->business_address ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Contact Person</label>
                    <input type="text" name="contact_person" class="flex-1 border rounded px-3 py-2" required value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Contact Phone</label>
                    <input type="text" name="contact_phone" class="flex-1 border rounded px-3 py-2" required value="{{ old('contact_phone', $supplier->contact_phone ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Contact Email</label>
                    <input type="email" name="contact_email" class="flex-1 border rounded px-3 py-2" required value="{{ old('contact_email', $supplier->contact_email ?? '') }}">
                </div>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-blue-800 mb-2">Dairy Farm Information</h2>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Farm Name</label>
                    <input type="text" name="farm_name" class="flex-1 border rounded px-3 py-2" required value="{{ old('farm_name', $dairyFarm->farm_name ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Farm Code</label>
                    <input type="text" name="farm_code" class="flex-1 border rounded px-3 py-2" required value="{{ old('farm_code', $dairyFarm->farm_code ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Farm Address</label>
                    <input type="text" name="farm_address" class="flex-1 border rounded px-3 py-2" required value="{{ old('farm_address', $dairyFarm->farm_address ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Farm Phone</label>
                    <input type="text" name="farm_phone" class="flex-1 border rounded px-3 py-2" required value="{{ old('farm_phone', $dairyFarm->farm_phone ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Farm Email</label>
                    <input type="email" name="farm_email" class="flex-1 border rounded px-3 py-2" required value="{{ old('farm_email', $dairyFarm->farm_email ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Farm Manager</label>
                    <input type="text" name="farm_manager" class="flex-1 border rounded px-3 py-2" required value="{{ old('farm_manager', $dairyFarm->farm_manager ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Manager Phone</label>
                    <input type="text" name="manager_phone" class="flex-1 border rounded px-3 py-2" value="{{ old('manager_phone', $dairyFarm->manager_phone ?? '') }}">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                    <label class="sm:w-40 font-semibold text-gray-700">Manager Email</label>
                    <input type="email" name="manager_email" class="flex-1 border rounded px-3 py-2" value="{{ old('manager_email', $dairyFarm->manager_email ?? '') }}">
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded font-semibold">Update Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection 