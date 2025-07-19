@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Edit Employee</h2>
    <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block font-semibold mb-1">Name</label>
            <input name="name" value="{{ old('name', $employee->name) }}" required class="border rounded px-3 py-2 w-full" />
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Role</label>
            <select name="role" required class="border rounded px-3 py-2 w-full">
                <option value="Warehouse Staff" @if($employee->role == 'Warehouse Staff') selected @endif>Warehouse Staff</option>
                <option value="Driver" @if($employee->role == 'Driver') selected @endif>Driver</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Assigned Vendor</label>
            <select name="vendor_id" class="border rounded px-3 py-2 w-full">
                <option value="">Unassigned</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @if($employee->vendor_id == $vendor->id) selected @endif>{{ $vendor->business_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Assigned Distribution Center</label>
            <select name="distribution_center_id" class="border rounded px-3 py-2 w-full">
                <option value="">Unassigned</option>
                @foreach($distributionCenters as $dc)
                    <option value="{{ $dc->id }}" @if($employee->distribution_center_id == $dc->id) selected @endif>{{ $dc->center_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Status</label>
            <select name="status" required class="border rounded px-3 py-2 w-full">
                <option value="Active" @if($employee->status == 'Active') selected @endif>Active</option>
                <option value="On Leave" @if($employee->status == 'On Leave') selected @endif>On Leave</option>
                <option value="Terminated" @if($employee->status == 'Terminated') selected @endif>Terminated</option>
            </select>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">Update</button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline px-6 py-2">Cancel</a>
        </div>
    </form>
</div>
@endsection 