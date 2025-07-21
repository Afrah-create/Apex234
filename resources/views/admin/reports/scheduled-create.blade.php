@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12">
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Schedule New Report</h1>
        <p class="text-gray-500 text-lg">Set up a new scheduled report for automatic delivery.</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif
        @if(
            \Illuminate\Support\Facades\Session::has('errors') &&
            count(\Illuminate\Support\Facades\Session::get('errors')) > 0
        )
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc pl-5">
                    @foreach(\Illuminate\Support\Facades\Session::get('errors')->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.reports.scheduled.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Name</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Description</label>
                <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Report Type</label>
                <select name="report_type" class="w-full border rounded px-3 py-2" required>
                    @foreach($reportTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Frequency</label>
                <select name="frequency" id="frequency" class="w-full border rounded px-3 py-2" required>
                    @foreach($frequencyOptions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4" id="day-of-week-group" style="display:none;">
                <label class="block text-sm font-semibold mb-1">Day of Week</label>
                <select name="day_of_week" class="w-full border rounded px-3 py-2">
                    <option value="">-- Select --</option>
                    @foreach($dayOfWeekOptions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4" id="day-of-month-group" style="display:none;">
                <label class="block text-sm font-semibold mb-1">Day of Month</label>
                <input type="number" name="day_of_month" min="1" max="31" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Time (HH:MM, 24h)</label>
                <input type="time" name="time" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Timezone</label>
                <input type="text" name="timezone" class="w-full border rounded px-3 py-2" value="{{ config('app.timezone', 'UTC') }}" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Recipients (comma-separated emails)</label>
                <input type="text" name="recipients" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Format</label>
                <select name="format" class="w-full border rounded px-3 py-2" required>
                    @foreach($formatOptions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" class="mr-2" checked>
                <label for="is_active" class="text-sm">Active</label>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">Schedule Report</button>
                <a href="{{ route('admin.reports.scheduled') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-semibold transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
    function updateFrequencyFields() {
        const freq = document.getElementById('frequency').value;
        document.getElementById('day-of-week-group').style.display = (freq === 'weekly') ? '' : 'none';
        document.getElementById('day-of-month-group').style.display = (freq === 'monthly' || freq === 'quarterly' || freq === 'yearly') ? '' : 'none';
    }
    document.getElementById('frequency').addEventListener('change', updateFrequencyFields);
    window.addEventListener('DOMContentLoaded', updateFrequencyFields);
</script>
@endsection 