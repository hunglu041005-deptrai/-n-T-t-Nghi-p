<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/payment.php';
require_once __DIR__ . '/includes/email.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    if ($isAjax) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Bạn cần đăng nhập để đặt sân.', 'redirect' => 'login.php']);
        exit;
    }
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$court_id      = intval($_POST['court_id'] ?? 0);
$date          = $_POST['booking_date'] ?? '';
$start_time    = $_POST['start_time'] ?? '';
$duration      = intval($_POST['duration'] ?? 1);
$payment_method = strtolower($_POST['payment_method'] ?? 'cash');
$notes         = trim($_POST['notes'] ?? '');

$error = '';

$court = getCourtById($court_id);
if (!$court) $error = 'Sân không tồn tại.';

if (!$date || !$start_time || !$duration || !$payment_method) {
    $error = 'Vui lòng cung cấp đầy đủ thông tin đặt sân.';
}

if (!$error) {
    $startDateTime = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $start_time);
    if (!$startDateTime) {
        // Thử format H:i:s
        $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $start_time);
    }
    if (!$startDateTime) {
        $error = 'Ngày giờ không hợp lệ: ' . $date . ' ' . $start_time;
    } else {
        $endDateTime = clone $startDateTime;
        $endDateTime->modify("+{$duration} hour");
        $end_time_full   = $endDateTime->format('H:i:00');
        $start_time_full = $startDateTime->format('H:i:00');
    }
}

if (!$error && !isSlotAvailable($court_id, $date, $start_time_full, $end_time_full)) {
    $error = 'Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác.';
}

if ($error) {
    if ($isAjax) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }
    $_SESSION['booking_error'] = $error;
    redirect("court.php?id={$court_id}&date={$date}");
}

// Lưu booking vào database
$total_price    = $court['price_per_hour'] * $duration;
$status         = 'confirmed';
$payment_status = ($payment_method === 'cash') ? 'unpaid' : 'pending';
$user_id        = (int) $_SESSION['user_id'];

$stmt = $mysqli->prepare(
    'INSERT INTO bookings (user_id, court_id, booking_date, start_time, end_time, total_price, payment_method, payment_status, status, notes)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->bind_param('iisssissss',
    $user_id, $court_id, $date,
    $start_time_full, $end_time_full,
    $total_price, $payment_method, $payment_status, $status, $notes
);

if (!$stmt->execute()) {
    $error = 'Lỗi khi lưu booking: ' . $stmt->error;
    $stmt->close();
    if ($isAjax) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }
    $_SESSION['booking_error'] = $error;
    redirect("court.php?id={$court_id}&date={$date}");
}

$booking_id = $stmt->insert_id;
$stmt->close();

// Xử lý theo phương thức thanh toán
if ($payment_method === 'vnpay' && class_exists('PaymentGateway')) {
    $vnpay_url = PaymentGateway::generateVNPayLink($booking_id, $total_price, 'Đặt sân - ' . $court['name'], $user_id);
    redirect($vnpay_url);

} elseif ($payment_method === 'momo' && class_exists('PaymentGateway')) {
    $_SESSION['momo_data'] = PaymentGateway::generateMoMoLink($booking_id, $total_price, 'Đặt sân - ' . $court['name'], $user_id);
    redirect('payment-momo.php?booking_id=' . $booking_id);

} else {
    // Tiền mặt hoặc fallback
    $_SESSION['booking_success'] = 'Đặt sân thành công! Vui lòng thanh toán khi đến sân.';

    if ($isAjax) {
        echo json_encode([
            'success'      => true,
            'booking_id'   => $booking_id,
            'message'      => 'Đặt sân thành công!',
            'court_name'   => $court['name'],
            'booking_date' => $date,
            'start_time'   => $start_time_full,
            'end_time'     => $end_time_full,
            'total_price'  => $total_price,
            'redirect'     => 'booking-history.php'
        ]);
        exit;
    }

    redirect('booking-history.php');
}
