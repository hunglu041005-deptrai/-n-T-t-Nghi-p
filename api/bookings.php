<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';
$response = ['success' => false, 'data' => null, 'message' => ''];

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true);
    $user_id = intval($payload['user_id'] ?? 0);
    $court_id = intval($payload['court_id'] ?? 0);
    $date = $payload['booking_date'] ?? '';
    $start_time = $payload['start_time'] ?? '';
    $duration = intval($payload['duration'] ?? 1);
    $payment_method = $payload['payment_method'] ?? 'cash';

    if (!$user_id || !$court_id || !$date || !$start_time || !$duration) {
        $response['message'] = 'Missing required booking fields.';
    } else {
        $startDateTime = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $start_time);
        if (!$startDateTime) {
            $response['message'] = 'Invalid booking date/time.';
        } else {
            $endDateTime = clone $startDateTime;
            $endDateTime->modify("+$duration hour");
            $end_time = $endDateTime->format('H:i:00');
            $start_time_full = $startDateTime->format('H:i:00');
            $stmt = $mysqli->prepare('SELECT COUNT(*) AS cnt FROM bookings WHERE court_id = ? AND booking_date = ? AND status != "cancelled" AND NOT (end_time <= ? OR start_time >= ?)');
            $stmt->bind_param('isss', $court_id, $date, $start_time_full, $end_time);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['cnt'] > 0) {
                $response['message'] = 'Khung giờ đã bị trùng.';
            } else {
                $stmt = $mysqli->prepare('SELECT price_per_hour FROM courts WHERE id = ? LIMIT 1');
                $stmt->bind_param('i', $court_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $court = $result->fetch_assoc();
                if (!$court) {
                    $response['message'] = 'Sân không tồn tại.';
                } else {
                    $total_price = intval($court['price_per_hour']) * $duration;
                    $status = 'pending';
                    $payment_status = 'pending';
                    $stmt = $mysqli->prepare('INSERT INTO bookings (user_id, court_id, booking_date, start_time, end_time, total_price, payment_method, payment_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('iisssisss', $user_id, $court_id, $date, $start_time_full, $end_time, $total_price, $payment_method, $payment_status, $status);
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['data'] = ['booking_id' => $stmt->insert_id, 'total_price' => $total_price];
                        $response['message'] = 'Đặt sân thành công.';
                    } else {
                        $response['message'] = 'Không thể tạo booking.';
                    }
                }
            }
        }
    }
} else {
    $response['message'] = 'Phương thức không hợp lệ.';
}

echo json_encode($response);
