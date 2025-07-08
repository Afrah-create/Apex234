@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white rounded shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}</h2>

    {{-- Notifications Section --}}
    @php $notifications = auth()->user()->notifications; @endphp
    @if($notifications->count())
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Notifications</h3>
            <ul class="mb-4">
                @foreach($notifications->take(5) as $notification)
                    <li class="mb-2 p-3 bg-blue-50 rounded">
                        <div>{{ $notification->data['message'] ?? 'You have a new notification.' }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($employee)
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Employee Details</h3>
            <ul class="mb-2">
                <li><strong>Name:</strong> {{ $employee->name }}</li>
                <li><strong>Role:</strong> {{ $employee->role }}</li>
                <li><strong>Status:</strong> {{ ucfirst($employee->status) }}</li>
            </ul>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-2">Vendor Details</h3>
            @if($employee->vendor)
                <ul>
                    <li><strong>Business Name:</strong> {{ $employee->vendor->business_name }}</li>
                    <li><strong>Address:</strong> {{ $employee->vendor->business_address ?? 'N/A' }}</li>
                    <li><strong>Phone:</strong> {{ $employee->vendor->phone_number ?? 'N/A' }}</li>
                    <li><strong>Contact Person:</strong> {{ $employee->vendor->contact_person ?? 'N/A' }}</li>
                    <li><strong>Contact Email:</strong> {{ $employee->vendor->contact_email ?? 'N/A' }}</li>
                    <li><strong>Status:</strong> {{ ucfirst($employee->vendor->status) }}</li>
                </ul>
            @else
                <p class="text-yellow-700">No vendor assigned.</p>
            @endif
        </div>
    @else
        <p class="text-red-600">No employee record found for your account.</p>
    @endif
</div>
@endsection 