// Function defined in the global scope
function displayNotification(message) {
    const notificationDropdown = document.querySelector('.notification-list');
    if (!notificationDropdown) return;

    const newNotification = document.createElement('li');
    newNotification.classList.add('notification-message');

    newNotification.innerHTML = `
        <a href="#">
            <div class="media d-flex">
                <div class="media-body flex-grow-1">
                    <p class="noti-details">
                        <span class="noti-title">${message.user_name}</span>
                        ${message.notification_text}
                    </p>
                    <p class="noti-time">
                        <span class="notification-time">${message.time_ago}</span>
                    </p>
                </div>
            </div>
        </a>
    `;

    notificationDropdown.prepend(newNotification);

    const badge = document.querySelector('.nav-item-box .badge');
    if (badge) {
        let count = parseInt(badge.textContent) || 0;
        badge.textContent = count + 1;
    }
}

// Attach to window for manual testing
window.displayNotification = displayNotification;

// Load past notifications
function loadPastNotifications() {
    fetch('/admin/notifications/fetch')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                // Show oldest first so latest ends up on top
                data.reverse().forEach(notification => displayNotification(notification));
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

// Socket connection
document.addEventListener('DOMContentLoaded', () => {
    
    const socketUrl = window.NOTIFICATION_SERVER_URL || `http://localhost:${window.NOTIFICATION_PORT}`;
    const socket = io(socketUrl);
    socket.on('connect', () => {
        const userId = window.Laravel.userId;
        if (userId) {
            socket.emit('register-user', userId);
        }
    });

    socket.on('notification', (message) => {
        displayNotification(message);
    });

    // Load old notifications on page load
    loadPastNotifications();
});

document.addEventListener('DOMContentLoaded', () => {
    // Existing code for Socket.IO connection and notifications

    // Clear All Notifications
    const clearButton = document.querySelector('.clear-noti');
    if (clearButton) {
        clearButton.addEventListener('click', clearAllNotifications);
    }
});

function clearAllNotifications() {
    fetch('/admin/notifications/clear', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.message);

        // Clear from dropdown
        const dropdown = document.querySelector('.notification-list');
        if (dropdown) {
            dropdown.innerHTML = '';  // Clear all notifications from the dropdown
        }

        // Reset badge
        const badge = document.querySelector('.nav-item-box .badge');
        if (badge) {
            badge.textContent = 0;  // Reset notification count badge
        }
    })
    .catch(error => console.error('Error clearing notifications:', error));
}

