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
                container.style.boxShadow = isOpen ? '' : '0 2px 8px rgba(37,99,235,0.08)';
            }
        }
    }
    </script>
    <div class="bg-white rounded-lg shadow divide-y divide-gray-100">
        @forelse($notifications as $notification)
            <div id="notification-container-{{ $notification->id }}" class="cursor-pointer transition" onclick="toggleNotificationDetails('{{ $notification->id }}')" style="border-bottom:1px solid #f3f4f6;">
                <div class="flex items-start gap-3 px-4 py-4 @if(!$notification->read_at) font-semibold bg-yellow-50 @else text-gray-700 @endif" style="align-items:center;">
                    <span class="mt-1" style="font-size:1.3rem;">🔔</span>
                    <span id="chevron-{{ $notification->id }}" class="mt-1 mr-2 transition-transform" style="display:inline-block; transform:rotate(0deg); font-size:1.3rem; color:#2563eb;">&#8250;</span>
                    <div class="flex-1">
                        <div class="text-sm" style="font-weight:600;">{{ $notification->data['title'] ?? $notification->data['message'] ?? 'New notification' }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <div id="notification-details-{{ $notification->id }}" style="display:none; background:#f9fafb; border-top:1px solid #e5e7eb;" class="px-8 pb-4">
                    <div class="text-xs text-gray-400 mb-2">Created: {{ $notification->created_at->format('M d, Y H:i') }}</div>
                    <div class="mb-2 text-gray-700">{{ $notification->data['message'] ?? '' }}</div>
                    @foreach($notification->data as $key => $value)
                        @if(!in_array($key, ['title', 'message']))
                            <div class="text-sm text-gray-500" style="margin-bottom:4px;">
                                <strong>{{ ucfirst($key) }}:</strong>
                                @if(is_array($value))
                                    <ul style="margin: 4px 0 4px 18px; padding: 0; list-style: disc;">
                                        @foreach($value as $subKey => $subValue)
                                            <li style="margin-bottom:2px;"><strong>{{ ucfirst(str_replace('_', ' ', $subKey)) }}:</strong> {{ is_array($subValue) ? json_encode($subValue) : $subValue }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $value }}
                                @endif
                            </div>
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