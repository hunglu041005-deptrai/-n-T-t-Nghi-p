<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/payment.php';
require_once __DIR__ . '/includes/email.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$court_id = intval($_POST['court_id'] ?? 0);
$date = $_POST['booking_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$duration = intval($_POST['duration'] ?? 1);
$payment_method = strtolower($_POST['payment_method'] ?? 'cash');

$error = '';
$court = getCourtById($court_id);
if (!$court) {
    $error = 'Sân không tồn tại.';
}

if (!$date || !$start_time || !$duration || !$payment_method) {
    $error = 'Vui lòng cung cấp đầy đủ thông tin đặt sân.';
}

$startDateTime = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $start_time);
if (!$startDateTime) {
    $error = 'Ngày giờ không hợp lệ.';
}
$endDateTime = clone $startDateTime;
$endDateTime->modify("+$duration hour");
$end_time = $endDateTime->format('H:i:00');
$start_time_full = $startDateTime->format('H:i:00');

if ($startDateTime->format('Y-m-d') !== $date) {
    $error = 'Ngày đặt sân phải hợp lệ.';
}

if (!$error && !isSlotAvailable($court_id, $date, $start_time_full, $end_time)) {
    $error = 'Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác.';
}

if ($error) {
    $_SESSION['booking_error'] = $error;
    redirect("court.php?id={$court_id}&date={$date}");
}

$total_price = $court['price_per_hour'] * $duration;
$stmt = $mysqli->prepare('INSERT INTO bookings (user_id, court_id, booking_date, start_time, end_time, total_price, payment_method, payment_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$status = 'pending';
$payment_status = ($payment_method === 'cash') ? 'unpaid' : 'pending';
$stmt->bind_param('iisssisss', $_SESSION['user_id'], $court_id, $date, $start_time_full, $end_time, $total_price, $payment_method, $payment_status, $status);
$stmt->execute();
$booking_id = $stmt->insert_id;
$stmt->close();

// Get user info for email
$user_email = $_SESSION['user_email'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Khách hàng';

// Prepare booking data for email
$booking_data = [
    'id' => $booking_id,
    'court_name' => $court['name'],
    'location' => $court['location'],
    'booking_date' => $date,
    'start_time' => $start_time_full,
    'end_time' => $end_time,
    'total_price' => $total_price,
    'status' => $status,
    'user_name' => $user_name,
    'user_email' => $user_email,
    'payment_method' => $payment_method
];

// Send confirmation email to user
EmailNotification::sendBookingConfirmation($user_email, $user_name, $booking_data);

// Notify admin
EmailNotification::notifyAdminBooking($booking_data);

// Handle payment based on method
if ($payment_method === 'vnpay') {
    // Generate VNPay payment link
    $vnpay_url = PaymentGateway::generateVNPayLink(
        $booking_id,
        $total_price,
        'Đặt sân cầu lông - ' . $court['name'],
        $_SESSION['user_id']
    );
    
    // Store payment URL in session for reference
    $_SESSION['payment_redirect'] = $vnpay_url;
    
    // Redirect to VNPay
    redirect($vnpay_url);
    
} elseif ($payment_method === 'momo') {
    // Generate MoMo payment request
    $momo_data = PaymentGateway::generateMoMoLink(
        $booking_id,
        $total_price,
        'Đặt sân cầu lông - ' . $court['name'],
        $_SESSION['user_id']
    );
    
    // In production, you'll POST this to MoMo's API
    // For now, store and redirect to payment page
    $_SESSION['momo_data'] = $momo_data;
    redirect('payment-momo.php?booking_id=' . $booking_id);
    
} elseif ($payment_method === 'cash') {
    // Mark as cash payment (unpaid, waiting for customer to pay at court)
    PaymentGateway::processCashPayment($booking_id);
    
    // Send success message
    $_SESSION['booking_success'] = 'Đặt sân thành công! Vui lòng thanh toán khi đến sân.';
    redirect('booking-history.php');
    
} else {
    // Invalid payment method
    $_SESSION['booking_error'] = 'Phương thức thanh toán không hợp lệ.';
    redirect("court.php?id={$court_id}&date={$date}");
}
