<!-- Notification Bell Component for Customer Pages -->
<div class="notification-bell-container" style="
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
">
    <button id="customerNotificationBell" class="notification-bell-btn" title="Notifications" aria-haspopup="true" aria-expanded="false" style="
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #2563eb;
        border: none;
        color: white;
        cursor: pointer;
        transition: background 0.2s;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    " onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#2563eb'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span id="customerNotificationDot" class="notification-dot" style="
            position: absolute;
            top: 1px;
            right: 1px;
            display: none;
            height: 12px;
            width: 12px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            font-size: 8px;
            font-weight: bold;
            align-items: center;
            justify-content: center;
        ">0</span>
    </button>
    
    <div id="customerNotificationDropdown" class="notification-dropdown" style="
        display: none;
        position: absolute;
        left: 50%;
        top: 35px;
        transform: translateX(-50%);
        width: 280px;
        max-width: 90vw;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 1000;
        border: 1px solid #e5e7eb;
    ">
        <div class="notification-header" style="
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
        ">
            <span style="font-weight: 600; color: #1f2937;">Notifications</span>
            <button id="customerMarkAllReadBtn" style="
                font-size: 12px;
                color: #2563eb;
                background: none;
                border: none;
                cursor: pointer;
                text-decoration: underline;
            ">Mark all as read</button>
        </div>
        
        <div id="customerNotificationListContainer" style="
            max-height: 300px;
            overflow-y: auto;
        ">
            <ul id="customerNotificationList" style="
                list-style: none;
                margin: 0;
                padding: 0;
            "></ul>
        </div>
        
        <div style="
            padding: 8px 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        ">
            <a href="{{ route('notifications.index') }}" style="
                color: #2563eb;
                font-size: 14px;
                text-decoration: underline;
            ">View all notifications</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('customerNotificationBell');
    const notificationDropdown = document.getElementById('customerNotificationDropdown');
    const notificationDot = document.getElementById('customerNotificationDot');
    const notificationList = document.getElementById('customerNotificationList');
    const markAllReadBtn = document.getElementById('customerMarkAllReadBtn');

    // Toggle notification dropdown
    notificationBell.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.style.display = notificationDropdown.style.display === 'none' ? 'block' : 'none';
        if (notificationDropdown.style.display === 'block') {
            loadNotifications();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.style.display = 'none';
        }
    });

    // Load notifications
    function loadNotifications() {
        fetch('/notifications/fetch')
            .then(response => response.json())
            .then(data => {
                notificationList.innerHTML = '';
                
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        const li = document.createElement('li');
                        li.style.cssText = `
                            padding: 12px 16px;
                            border-bottom: 1px solid #f3f4f6;
                            cursor: pointer;
                            transition: background 0.2s;
                        `;
                        li.onmouseover = () => li.style.background = '#f9fafb';
                        li.onmouseout = () => li.style.background = 'transparent';
                        
                        li.innerHTML = `
                            <div style="font-size: 14px; color: #1f2937; margin-bottom: 4px;">
                                ${notification.data.title || notification.data.message || 'New notification'}
                            </div>
                            <div style="font-size: 12px; color: #6b7280;">
                                ${new Date(notification.created_at).toLocaleString()}
                            </div>
                        `;
                        
                        li.addEventListener('click', () => {
                            markAsRead(notification.id);
                            if (notification.data.url) {
                                window.location.href = notification.data.url;
                            }
                        });
                        
                        notificationList.appendChild(li);
                    });
                } else {
                    notificationList.innerHTML = `
                        <li style="padding: 20px; text-align: center; color: #6b7280;">
                            No notifications
                        </li>
                    `;
                }
                
                // Update notification count
                updateNotificationCount(data.unread_count || 0);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = `
                    <li style="padding: 20px; text-align: center; color: #ef4444;">
                        Error loading notifications
                    </li>
                `;
            });
    }

    // Mark notification as read
    function markAsRead(notificationId) {
        fetch(`/api/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    });

    // Update notification count
    function updateNotificationCount(count) {
        if (count > 0) {
            notificationDot.style.display = 'flex';
            notificationDot.textContent = count > 99 ? '99+' : count;
        } else {
            notificationDot.style.display = 'none';
        }
    }

    // Load initial notification count
    loadNotifications();

    // Set up Echo for real-time notifications
    if (window.Echo) {
        window.Echo.private(`App.Models.User.${window.Laravel.userId}`)
            .notification((notification) => {
                // Show a toast notification
                showToast(notification.data.title || notification.data.message || 'New notification');
                
                // Reload notifications if dropdown is open
                if (notificationDropdown.style.display === 'block') {
                    loadNotifications();
                }
            });
    }

    // Simple toast notification function
    function showToast(message) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            font-size: 14px;
            max-width: 300px;
            word-wrap: break-word;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
});
</script> 