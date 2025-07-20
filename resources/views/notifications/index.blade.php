@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">All Notifications</h1>
    <script>
    function toggleNotificationDetails(id) {
        const details = document.getElementById('notification-details-' + id);
        const chevron = document.getElementById('chevron-' + id);
        if (details) {
            const isOpen = details.style.display === 'block';
            details.style.display = isOpen ? 'none' : 'block';
            if (chevron) {
                chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
            }
            // Optionally, highlight the expanded notification
            const container = document.getElementById('notification-container-' + id);
            if (container) {
                container.style.background = isOpen ? '' : '#e0f2fe';
            }
        }
    }
    </script>
    <div class="bg-white rounded-lg shadow divide-y divide-gray-100">
        @forelse($notifications as $notification)
            <div id="notification-container-{{ $notification->id }}" class="cursor-pointer transition" onclick="toggleNotificationDetails('{{ $notification->id }}')">
                <div class="flex items-start gap-3 px-4 py-4 @if(!$notification->read_at) font-semibold bg-yellow-50 @else text-gray-700 @endif">
                    <span class="mt-1"><svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg></span>
                    <span id="chevron-{{ $notification->id }}" class="mt-1 mr-2 transition-transform" style="display:inline-block; transform:rotate(0deg);">&#8250;</span>
                    <div class="flex-1">
                        <div class="text-sm">{{ $notification->data['message'] ?? 'New notification' }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <div id="notification-details-{{ $notification->id }}" style="display:none; background:#f9fafb;" class="px-8 pb-4">
                    <div class="text-xs text-gray-400 mb-2">Created: {{ $notification->created_at->format('M d, Y H:i') }}</div>
                    <div class="mb-2 text-gray-700">{{ $notification->data['message'] ?? '' }}</div>
                    @foreach($notification->data as $key => $value)
                        @if(!in_array($key, ['title', 'message']))
                            <div class="text-sm text-gray-500"><strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</div>
                        @endif
                    @endforeach
                    @if($notification->read_at)
                        <div class="text-xs text-gray-400 mb-2">Read: {{ $notification->read_at->format('M d, Y H:i') }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6 text-gray-500 text-center">No notifications found.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $notifications->links() }}</div>
</div>
@endsection 