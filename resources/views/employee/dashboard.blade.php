@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white rounded shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}</h2>

    @if($employee && in_array($employee->role, ['Warehouse Staff', 'Driver']))
        <div class="text-center text-lg text-gray-700 mt-8">
            Please use the navigation to access your {{ $employee->role }} dashboard.
        </div>
    @else
        <div class="text-center text-lg text-gray-500 mt-8">
            This dashboard is only available for Warehouse Staff and Driver roles.
        </div>
    @endif
</div>
<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
@endsection 