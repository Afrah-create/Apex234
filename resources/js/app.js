console.log('app.js loaded');
import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const userId = window.Laravel && window.Laravel.userId;
    if (!userId) return;

    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    const list = document.getElementById('notificationList');
    const dot = document.getElementById('notificationDot');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    let unreadCount = 0;

    // Fetch notifications via AJAX
    async function fetchNotifications() {
        try {
            const res = await fetch('/notifications/fetch');
            const data = await res.json();
            renderNotifications(data.notifications || []);
        } catch (e) {
            list.innerHTML = '<li class="p-4 text-gray-500 text-center">Failed to load notifications.</li>';
        }
    }

    // Render notifications in dropdown
    function renderNotifications(notifications) {
        list.innerHTML = '';
        unreadCount = 0;
        if (!notifications.length) {
            list.innerHTML = '<li class="p-4 text-gray-500 text-center">No notifications.</li>';
            dot.classList.add('hidden');
            return;
        }
        notifications.forEach(n => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 transition';
            if (!n.read_at) {
                li.classList.add('font-semibold', 'bg-yellow-50');
                unreadCount++;
            } else {
                li.classList.add('text-gray-700');
            }
            li.innerHTML = `
                <span class="mt-1"><svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg></span>
                <div class="flex-1">
                    <div class="text-sm">${n.data.message || 'New notification'}</div>
                    <div class="text-xs text-gray-400 mt-1">${n.created_at ? new Date(n.created_at).toLocaleString() : ''}</div>
                </div>
            `;
            li.onclick = () => {
                if (n.data && n.data.order_id) {
                    window.location.href = `/admin/orders/${n.data.order_id}`;
                }
            };
            list.appendChild(li);
        });
        if (unreadCount > 0) {
            dot.textContent = unreadCount;
            dot.classList.remove('hidden');
        } else {
            dot.classList.add('hidden');
        }
    }

    // Mark all as read
    if (markAllReadBtn) {
        markAllReadBtn.onclick = async function() {
            await fetch('/notifications/mark-all-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
            fetchNotifications();
        };
    }

    // Toggle dropdown and fetch notifications
    if (bell) {
        bell.addEventListener('click', function (e) {
            e.preventDefault();
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                fetchNotifications();
            }
        });
    }

    // Real-time: Listen for new notifications
    window.Echo.private('App.Models.User.' + userId)
        .notification((notification) => {
            fetchNotifications();
        });
});
