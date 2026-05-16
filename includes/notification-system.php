<?php
require_once __DIR__ . '/functions.php';

class NotificationSystem {
    private $mysqli;
    
    public function __construct() {
        global $mysqli;
        $this->mysqli = $mysqli;
    }
    
    /**
     * Create a new notification
     */
    public function createNotification($data) {
        $stmt = $this->mysqli->prepare('
            INSERT INTO booking_notifications (booking_id, user_id, type, title, message, sent_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ');
        
        $stmt->bind_param('iisss', 
            $data['booking_id'],
            $data['user_id'],
            $data['type'],
            $data['title'],
            $data['message']
        );
        
        if ($stmt->execute()) {
            $notificationId = $this->mysqli->insert_id;
            
            // Send real-time notification via WebSocket
            $this->sendRealTimeNotification($data['user_id'], [
                'id' => $notificationId,
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return $notificationId;
        }
        
        return false;
    }
    
    /**
     * Get user notifications
     */
    public function getUserNotifications($userId, $limit = 20, $offset = 0) {
        $stmt = $this->mysqli->prepare('
            SELECT n.*, b.booking_date, b.start_time, c.name as court_name
            FROM booking_notifications n
            LEFT JOIN bookings b ON n.booking_id = b.id
            LEFT JOIN courts c ON b.court_id = c.id
            WHERE n.user_id = ?
            ORDER BY n.sent_at DESC
            LIMIT ? OFFSET ?
        ');
        
        $stmt->bind_param('iii', $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        return $notifications;
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId) {
        $stmt = $this->mysqli->prepare('
            UPDATE booking_notifications 
            SET is_read = 1 
            WHERE id = ? AND user_id = ?
        ');
        
        $stmt->bind_param('ii', $notificationId, $userId);
        return $stmt->execute();
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId) {
        $stmt = $this->mysqli->prepare('
            UPDATE booking_notifications 
            SET is_read = 1 
            WHERE user_id = ? AND is_read = 0
        ');
        
        $stmt->bind_param('i', $userId);
        return $stmt->execute();
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount($userId) {
        $stmt = $this->mysqli->prepare('
            SELECT COUNT(*) as count 
            FROM booking_notifications 
            WHERE user_id = ? AND is_read = 0
        ');
        
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result['count'];
    }
    
    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmation($bookingId) {
        $booking = $this->getBookingDetails($bookingId);
        
        if ($booking) {
            $this->createNotification([
                'booking_id' => $bookingId,
                'user_id' => $booking['user_id'],
                'type' => 'confirmation',
                'title' => 'Đặt sân thành công!',
                'message' => "Bạn đã đặt thành công sân {$booking['court_name']} vào {$booking['booking_date']} lúc {$booking['start_time']}"
            ]);
        }
    }
    
    /**
     * Send booking reminder
     */
    public function sendBookingReminder($bookingId) {
        $booking = $this->getBookingDetails($bookingId);
        
        if ($booking) {
            $this->createNotification([
                'booking_id' => $bookingId,
                'user_id' => $booking['user_id'],
                'type' => 'reminder',
                'title' => 'Nhắc nhở: Sắp đến giờ chơi!',
                'message' => "Bạn có lịch chơi tại {$booking['court_name']} vào {$booking['start_time']} hôm nay"
            ]);
        }
    }
    
    /**
     * Send cancellation notification
     */
    public function sendCancellationNotification($bookingId) {
        $booking = $this->getBookingDetails($bookingId);
        
        if ($booking) {
            $this->createNotification([
                'booking_id' => $bookingId,
                'user_id' => $booking['user_id'],
                'type' => 'cancellation',
                'title' => 'Booking đã được hủy',
                'message' => "Booking sân {$booking['court_name']} vào {$booking['booking_date']} đã được hủy thành công"
            ]);
        }
    }
    
    /**
     * Send real-time notification via WebSocket
     */
    private function sendRealTimeNotification($userId, $data) {
        // This would integrate with a WebSocket server (like Socket.IO, Pusher, etc.)
        // For now, we'll store it for polling-based updates
        
        $cacheFile = __DIR__ . "/../cache/notifications_user_{$userId}.json";
        
        // Ensure cache directory exists
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        // Load existing notifications
        $notifications = [];
        if (file_exists($cacheFile)) {
            $notifications = json_decode(file_get_contents($cacheFile), true) ?: [];
        }
        
        // Add new notification
        array_unshift($notifications, $data);
        
        // Keep only last 50 notifications
        $notifications = array_slice($notifications, 0, 50);
        
        // Save to cache
        file_put_contents($cacheFile, json_encode($notifications));
    }
    
    /**
     * Get real-time notifications for polling
     */
    public function getRealTimeNotifications($userId, $lastTimestamp = null) {
        $cacheFile = __DIR__ . "/../cache/notifications_user_{$userId}.json";
        
        if (!file_exists($cacheFile)) {
            return [];
        }
        
        $notifications = json_decode(file_get_contents($cacheFile), true) ?: [];
        
        if ($lastTimestamp) {
            $notifications = array_filter($notifications, function($notification) use ($lastTimestamp) {
                return strtotime($notification['timestamp']) > strtotime($lastTimestamp);
            });
        }
        
        return array_values($notifications);
    }
    
    /**
     * Schedule booking reminders
     */
    public function scheduleReminders() {
        // Get bookings that need reminders (2 hours before start time)
        $stmt = $this->mysqli->prepare('
            SELECT b.*, c.name as court_name
            FROM bookings b
            JOIN courts c ON b.court_id = c.id
            WHERE b.booking_date = CURDATE()
            AND TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(b.booking_date, " ", b.start_time)) BETWEEN 115 AND 125
            AND b.status = "confirmed"
            AND NOT EXISTS (
                SELECT 1 FROM booking_notifications 
                WHERE booking_id = b.id AND type = "reminder"
            )
        ');
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($booking = $result->fetch_assoc()) {
            $this->sendBookingReminder($booking['id']);
        }
    }
    
    private function getBookingDetails($bookingId) {
        $stmt = $this->mysqli->prepare('
            SELECT b.*, c.name as court_name, u.name as user_name
            FROM bookings b
            JOIN courts c ON b.court_id = c.id
            JOIN users u ON b.user_id = u.id
            WHERE b.id = ?
        ');
        
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

// Cron job function to send scheduled reminders
function sendScheduledReminders() {
    $notificationSystem = new NotificationSystem();
    $notificationSystem->scheduleReminders();
}
?>