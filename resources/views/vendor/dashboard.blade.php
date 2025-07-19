@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Vendor Dashboard</h1>
    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('vendor.production.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Production</a>
        <a href="{{ route('vendor.reports') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Reports</a>
    </div>
    <div>
        <img src="" alt="Vendor Image" style="max-width:200px;">
    </div>
    <div class="mt-4">
        <h2>Vendor Stats</h2>
        <ul>
            <li>Total Orders: <!-- Add dynamic value here --></li>
            <li>Pending Shipments: <!-- Add dynamic value here --></li>
        </ul>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Assigned Employees</h2>
        @if($employees->count())
            <table class="min-w-full bg-white border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Role</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr>
                            <td class="px-4 py-2">{{ $employee->name }}</td>
                            <td class="px-4 py-2">{{ $employee->role }}</td>
                            <td class="px-4 py-2">{{ ucfirst($employee->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-600">No employees assigned to this vendor yet.</p>
        @endif
    </div>
</div>
<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // The My Reports table and script are removed as per the edit hint.
    });
</script>
@endsection 