// Real-time Notification System
class NotificationManager {
    constructor() {
        this.lastTimestamp = null;
        this.pollingInterval = null;
        this.isPolling = false;
        
        this.init();
    }
    
    init() {
        this.createNotificationUI();
        this.loadInitialNotifications();
        this.startPolling();
        this.bindEvents();
    }
    
    createNotificationUI() {
        // Add notification bell to navbar
        const navbar = document.querySelector('.navbar-nav.ms-auto');
        if (navbar && !document.getElementById('notificationDropdown')) {
            const notificationHTML = `
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" 
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              id="notificationBadge" style="display: none;">
                            0
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown" 
                        aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span>Thông báo</span>
                            <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
                                Đánh dấu đã đọc
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <div id="notificationList">
                            <li class="dropdown-item text-center text-muted">
                                <i class="fas fa-spinner fa-spin me-2"></i>Đang tải...
                            </li>
                        </div>
                        <li><hr class="dropdown-divider"></li>
                        <li class="dropdown-item text-center">
                            <a href="#" class="text-decoration-none" id="viewAllNotifications">
                                Xem tất cả thông báo
                            </a>
                        </li>
                    </ul>
                </li>
            `;
            
            // Insert before the first nav-item (usually login/user menu)
            const firstNavItem = navbar.querySelector('.nav-item');
            if (firstNavItem) {
                firstNavItem.insertAdjacentHTML('beforebegin', notificationHTML);
            }
        }
    }
    
    async loadInitialNotifications() {
        try {
            const response = await fetch('/api/notifications.php?action=list&limit=10');
            const data = await response.json();
            
            if (data.success) {
                this.updateNotificationUI(data.notifications);
                this.updateBadge(data.unread_count);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }
    
    startPolling() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        this.pollingInterval = setInterval(() => {
            this.pollForUpdates();
        }, 30000); // Poll every 30 seconds
    }
    
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        this.isPolling = false;
    }
    
    async pollForUpdates() {
        try {
            const url = `/api/notifications.php?action=realtime${this.lastTimestamp ? '&last_timestamp=' + encodeURIComponent(this.lastTimestamp) : ''}`;
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success && data.notifications.length > 0) {
                this.handleNewNotifications(data.notifications);
                this.lastTimestamp = data.timestamp;
            }
        } catch (error) {
            console.error('Error polling for updates:', error);
        }
    }
    
    handleNewNotifications(notifications) {
        notifications.forEach(notification => {
            this.showToastNotification(notification);
        });
        
        // Reload notification list
        this.loadInitialNotifications();
    }
    
    showToastNotification(notification) {
        // Create toast notification
        const toastHTML = `
            <div class="toast notification-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="toast-header">
                    <i class="fas fa-bell text-primary me-2"></i>
                    <strong class="me-auto">${this.escapeHtml(notification.title)}</strong>
                    <small class="text-muted">Vừa xong</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${this.escapeHtml(notification.message)}
                </div>
            </div>
        `;
        
        // Add to toast container
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        // Show toast
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
        
        // Play notification sound
        this.playNotificationSound();
    }
    
    updateNotificationUI(notifications) {
        const notificationList = document.getElementById('notificationList');
        if (!notificationList) return;
        
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <li class="dropdown-item text-center text-muted py-3">
                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                    Không có thông báo nào
                </li>
            `;
            return;
        }
        
        const notificationHTML = notifications.map(notification => `
            <li class="dropdown-item notification-item ${notification.is_read ? '' : 'unread'}" 
                data-notification-id="${notification.id}">
                <div class="d-flex">
                    <div class="notification-icon me-2">
                        <i class="fas ${this.getNotificationIcon(notification.type)} text-${this.getNotificationColor(notification.type)}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="notification-title fw-bold">${this.escapeHtml(notification.title)}</div>
                        <div class="notification-message text-muted small">${this.escapeHtml(notification.message)}</div>
                        <div class="notification-time text-muted small">
                            <i class="fas fa-clock me-1"></i>${this.formatTime(notification.sent_at)}
                        </div>
                    </div>
                    ${!notification.is_read ? '<div class="notification-dot bg-primary rounded-circle"></div>' : ''}
                </div>
            </li>
        `).join('');
        
        notificationList.innerHTML = notificationHTML;
    }
    
    updateBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
    }
    
    bindEvents() {
        // Mark all as read
        document.addEventListener('click', async (e) => {
            if (e.target.id === 'markAllReadBtn') {
                e.preventDefault();
                await this.markAllAsRead();
            }
        });
        
        // Mark individual notification as read
        document.addEventListener('click', async (e) => {
            const notificationItem = e.target.closest('.notification-item');
            if (notificationItem && notificationItem.classList.contains('unread')) {
                const notificationId = notificationItem.dataset.notificationId;
                await this.markAsRead(notificationId);
                notificationItem.classList.remove('unread');
                
                // Update badge
                const currentCount = parseInt(document.getElementById('notificationBadge').textContent) || 0;
                this.updateBadge(Math.max(0, currentCount - 1));
            }
        });
        
        // Handle page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopPolling();
            } else {
                this.startPolling();
            }
        });
    }
    
    async markAsRead(notificationId) {
        try {
            const formData = new FormData();
            formData.append('notification_id', notificationId);
            
            await fetch('/api/notifications.php?action=mark_read', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    
    async markAllAsRead() {
        try {
            await fetch('/api/notifications.php?action=mark_all_read', {
                method: 'POST'
            });
            
            // Update UI
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            
            this.updateBadge(0);
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }
    
    getNotificationIcon(type) {
        const icons = {
            'confirmation': 'fa-check-circle',
            'reminder': 'fa-clock',
            'cancellation': 'fa-times-circle',
            'update': 'fa-info-circle'
        };
        return icons[type] || 'fa-bell';
    }
    
    getNotificationColor(type) {
        const colors = {
            'confirmation': 'success',
            'reminder': 'warning',
            'cancellation': 'danger',
            'update': 'info'
        };
        return colors[type] || 'primary';
    }
    
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'Vừa xong';
        if (diffInMinutes < 60) return `${diffInMinutes} phút trước`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)} giờ trước`;
        return `${Math.floor(diffInMinutes / 1440)} ngày trước`;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    playNotificationSound() {
        // Create and play notification sound
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
        audio.volume = 0.3;
        audio.play().catch(() => {
            // Ignore errors if audio can't be played
        });
    }
}

// Initialize notification system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if user is logged in
    if (document.querySelector('.navbar-nav .dropdown-toggle')) {
        window.notificationManager = new NotificationManager();
    }
});