<div class="notification-widget" id="notificationWidget">
    <div class="notification-icon" onclick="toggleNotifications(event)">
        <i class="fas fa-bell"></i>
        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
    </div>

    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h6>Notifications</h6>
            <button onclick="markAllAsRead()" class="btn-mark-all">Tout marquer comme lu</button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="loading">Chargement...</div>
        </div>
    </div>
</div>

<style>
.notification-widget {
    position: relative;
    display: inline-block;
}

.notification-icon {
    position: relative;
    cursor: pointer;
    font-size: 1.2rem;
    color: var(--gm);
    transition: var(--t);
}

.notification-icon:hover {
    color: var(--vp);
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 50%;
    min-width: 18px;
    text-align: center;
}

.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 380px;
    max-height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    z-index: 99999;
    margin-top: 10px;
    overflow: hidden;
    display: none;
}

.notification-dropdown.show {
    display: block !important;
}

.notification-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h6 {
    margin: 0;
    font-weight: 700;
    color: #2d3a3e;
}

.btn-mark-all {
    background: none;
    border: none;
    color: #2e7d64;
    font-size: 0.7rem;
    cursor: pointer;
    font-weight: 600;
}

.btn-mark-all:hover {
    text-decoration: underline;
}

.notification-list {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    padding: 12px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.notification-item.unread {
    background: #f0fdf4;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-icon-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e8f5f1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2e7d64;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-message {
    font-size: 0.85rem;
    color: #2d3a3e;
    margin-bottom: 4px;
}

.notification-time {
    font-size: 0.7rem;
    color: #9ca3af;
}

.notification-empty {
    padding: 40px;
    text-align: center;
    color: #9ca3af;
}

.loading {
    padding: 40px;
    text-align: center;
    color: #9ca3af;
}

@media (max-width: 768px) {
    .notification-dropdown {
        width: 320px;
        right: -60px;
    }
}

/* Animation d'entrée */
@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-dropdown.show {
    animation: dropdownFadeIn 0.2s ease-out;
}
</style>

<script>
let notificationCheckInterval;
let isDropdownOpen = false;

function toggleNotifications(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const dropdown = document.getElementById('notificationDropdown');

    if (isDropdownOpen) {
        dropdown.classList.remove('show');
        isDropdownOpen = false;
    } else {
        dropdown.classList.add('show');
        isDropdownOpen = true;
        loadNotifications();
    }
}

async function loadNotifications() {
    try {
        const response = await fetch('{{ route("admin.notifications.unread") }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            updateNotificationUI(data.count, data.notifications);
        }
    } catch (error) {
        console.error('Erreur chargement notifications:', error);
        const list = document.getElementById('notificationList');
        if (list) {
            list.innerHTML = '<div class="notification-empty">Erreur de chargement</div>';
        }
    }
}

function updateNotificationUI(count, notifications) {
    const badge = document.getElementById('notificationCount');
    const list = document.getElementById('notificationList');

    if (count > 0) {
        badge.style.display = 'inline-flex';
        badge.textContent = count > 99 ? '99+' : count;
    } else {
        badge.style.display = 'none';
    }

    if (!notifications || notifications.length === 0) {
        list.innerHTML = '<div class="notification-empty">Aucune notification</div>';
        return;
    }

    list.innerHTML = notifications.map(notif => `
        <div class="notification-item ${!notif.is_read ? 'unread' : ''}" onclick="markAsRead(${notif.id})">
            <div class="notification-icon-small">
                <i class="fas ${getIconForType(notif.type)}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-message">${escapeHtml(notif.message)}</div>
                <div class="notification-time">${formatDate(notif.created_at)}</div>
            </div>
        </div>
    `).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getIconForType(type) {
    const icons = {
        'new_contact': 'fa-envelope',
        'status_changed': 'fa-exchange-alt'
    };
    return icons[type] || 'fa-bell';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffSeconds = Math.floor((now - date) / 1000);

    if (diffSeconds < 60) return 'À l\'instant';
    if (diffSeconds < 3600) return `Il y a ${Math.floor(diffSeconds / 60)} min`;
    if (diffSeconds < 86400) return `Il y a ${Math.floor(diffSeconds / 3600)} h`;
    if (diffSeconds < 604800) return `Il y a ${Math.floor(diffSeconds / 86400)} j`;

    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
}

async function markAsRead(id) {
    try {
        const response = await fetch(`{{ url("admin/notifications") }}/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            loadNotifications();
            // Mettre à jour le badge
            const countResponse = await fetch('{{ route("admin.notifications.unread") }}', {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await countResponse.json();
            if (data.success) {
                const badge = document.getElementById('notificationCount');
                if (data.count > 0) {
                    badge.style.display = 'inline-flex';
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch('{{ route("admin.notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            loadNotifications();
            const badge = document.getElementById('notificationCount');
            badge.style.display = 'none';
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Fermer le dropdown si on clique en dehors
document.addEventListener('click', function(event) {
    const widget = document.getElementById('notificationWidget');
    const dropdown = document.getElementById('notificationDropdown');

    if (widget && dropdown && !widget.contains(event.target) && isDropdownOpen) {
        dropdown.classList.remove('show');
        isDropdownOpen = false;
    }
});

// Vérifier les nouvelles notifications toutes les 30 secondes
function startNotificationPolling() {
    if (notificationCheckInterval) clearInterval(notificationCheckInterval);
    loadNotifications();
    notificationCheckInterval = setInterval(loadNotifications, 30000);
}

// Démarrer le polling
startNotificationPolling();
</script>
