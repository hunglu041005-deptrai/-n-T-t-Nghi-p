<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/notification-system.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$notificationSystem = new NotificationSystem();
$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $limit = intval($_GET['limit'] ?? 20);
        $offset = intval($_GET['offset'] ?? 0);
        
        $notifications = $notificationSystem->getUserNotifications($userId, $limit, $offset);
        $unreadCount = $notificationSystem->getUnreadCount($userId);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
        break;
        
    case 'mark_read':
        $notificationId = intval($_POST['notification_id'] ?? 0);
        
        if ($notificationId) {
            $success = $notificationSystem->markAsRead($notificationId, $userId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
        }
        break;
        
    case 'mark_all_read':
        $success = $notificationSystem->markAllAsRead($userId);
        echo json_encode(['success' => $success]);
        break;
        
    case 'unread_count':
        $count = $notificationSystem->getUnreadCount($userId);
        echo json_encode(['success' => true, 'count' => $count]);
        break;
        
    case 'realtime':
        $lastTimestamp = $_GET['last_timestamp'] ?? null;
        $notifications = $notificationSystem->getRealTimeNotifications($userId, $lastTimestamp);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>