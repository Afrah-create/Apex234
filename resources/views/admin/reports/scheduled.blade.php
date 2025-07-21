@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-12">
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Scheduled Reports</h1>
        <p class="text-gray-500 text-lg">View and manage all scheduled reports and their delivery settings.</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="mb-4 flex items-center gap-4">
            <a href="{{ route('admin.reports.scheduled.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">Schedule New Report</a>
        </div>
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif
        <form id="bulk-delete-form" method="POST" action="{{ route('admin.reports.scheduled.bulkDelete') }}">
            @csrf
            @method('DELETE')
            <div class="mb-4 flex items-center gap-4">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition" onclick="return confirm('Are you sure you want to delete the selected scheduled reports?')">Delete Selected</button>
            </div>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-3 py-2 text-left"><input type="checkbox" id="select-all"></th>
                        <th class="px-3 py-2 text-left">Name</th>
                        <th class="px-3 py-2 text-left">Description</th>
                        <th class="px-3 py-2 text-left">Frequency</th>
                        <th class="px-3 py-2 text-left">Next Run</th>
                        <th class="px-3 py-2 text-left">Recipients</th>
                        <th class="px-3 py-2 text-center">Status</th>
                        <th class="px-3 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scheduledReports as $report)
                        <tr>
                            <td class="px-3 py-2"><input type="checkbox" name="ids[]" value="{{ $report->id }}"></td>
                            <td class="px-3 py-2">{{ $report->name }}</td>
                            <td class="px-3 py-2">{{ $report->description }}</td>
                            <td class="px-3 py-2">{{ ucfirst($report->frequency) }}</td>
                            <td class="px-3 py-2">{{ $report->next_generation_at ? $report->next_generation_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="px-3 py-2">@if(is_array($report->recipients)){{ implode(', ', $report->recipients) }}@else{{ $report->recipients }}@endif</td>
                            <td class="px-3 py-2 text-center">
                                @if($report->is_active)
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">Active</span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-gray-200 text-gray-600 rounded text-xs font-semibold">Inactive</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold run-now-btn" data-report-id="{{ $report->id }}">Run Now</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-6 text-center text-gray-400">No scheduled reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
        @foreach($scheduledReports as $report)
            <form method="POST" action="{{ route('admin.reports.scheduled.runNow', $report->id) }}" id="run-now-form-{{ $report->id }}" style="display:none">
                @csrf
            </form>
        @endforeach
        <script>
            document.querySelectorAll('.run-now-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if(confirm('Run this report now?')) {
                        document.getElementById('run-now-form-' + btn.getAttribute('data-report-id')).submit();
                    }
                });
            });
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="ids[]"]');
                for (const cb of checkboxes) {
                    cb.checked = this.checked;
                }
            });
        </script>
    </div>
</div>
@endsection 