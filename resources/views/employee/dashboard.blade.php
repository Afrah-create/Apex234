@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white rounded shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}</h2>
    @if(auth()->user()->employee)
        <ul class="mb-4">
            <li><strong>Role:</strong> {{ auth()->user()->employee->role }}</li>
            <li><strong>Status:</strong> {{ auth()->user()->employee->status }}</li>
            <li><strong>Vendor:</strong> {{ auth()->user()->employee->vendor ? auth()->user()->employee->vendor->name : 'Unassigned' }}</li>
        </ul>
        {{-- Add more employee-specific info or actions here --}}
    @else
        <p class="text-red-600">No employee record found for your account.</p>
    @endif
</div>
@endsection 