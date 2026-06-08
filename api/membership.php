<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Bạn cần đăng nhập.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$plans = [
    1 => ['name'=>'COMBO CHIỀU 14H–17H','detail'=>'10 VÉ TẶNG 1 VÉ','price'=>720000,'months'=>3,'free'=>11],
    2 => ['name'=>'COMBO CHIỀU 14H–17H','detail'=>'20 VÉ TẶNG 2 VÉ','price'=>1440000,'months'=>6,'free'=>22],
    3 => ['name'=>'COMBO TỐI 20H–22H','detail'=>'20 VÉ TẶNG 2 VÉ','price'=>1440000,'months'=>9,'free'=>22],
    4 => ['name'=>'COMBO TỐI 20H–22H','detail'=>'30 VÉ TẶNG 3 VÉ','price'=>2160000,'months'=>12,'free'=>33],
    5 => ['name'=>'COMBO CHIỀU 15H–18H','detail'=>'10 VÉ TẶNG 1 VÉ','price'=>720000,'months'=>3,'free'=>11],
    6 => ['name'=>'COMBO CHIỀU 15H–18H','detail'=>'20 VÉ TẶNG 2 VÉ','price'=>1440000,'months'=>6,'free'=>22],
];

$plan_id        = intval($_POST['plan_id'] ?? 0);
$payment_method = strtolower($_POST['payment_method'] ?? 'cash');

if (!isset($plans[$plan_id])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Gói không hợp lệ.']);
    exit;
}

$plan       = $plans[$plan_id];
$user_id    = (int) $_SESSION['user_id'];
$start_date = date('Y-m-d');
$end_date   = date('Y-m-d', strtotime("+{$plan['months']} months"));

// Sinh mã thẻ hội viên duy nhất: HV + năm + 6 số ngẫu nhiên
do {
    $member_code = 'HV' . date('Y') . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    $check = $mysqli->prepare('SELECT id FROM memberships WHERE member_code = ?');
    $check->bind_param('s', $member_code);
    $check->execute();
    $check->store_result();
    $exists = $check->num_rows > 0;
    $check->close();
} while ($exists);

$payment_status = ($payment_method === 'cash') ? 'pending' : 'paid';
$status         = 'active';

$stmt = $mysqli->prepare(
    'INSERT INTO memberships (user_id, plan_id, plan_name, plan_detail, price, months, free_tickets, payment_method, payment_status, status, member_code, start_date, end_date)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->bind_param(
    'iisssiissssss',
    $user_id, $plan_id,
    $plan['name'], $plan['detail'],
    $plan['price'], $plan['months'], $plan['free'],
    $payment_method, $payment_status, $status,
    $member_code, $start_date, $end_date
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lỗi lưu dữ liệu: ' . $stmt->error]);
    exit;
}

$membership_id = $stmt->insert_id;
$stmt->close();

// Gửi thông báo kích hoạt hội viên
try {
    require_once __DIR__ . '/../includes/notification-system.php';
    $ns = new NotificationSystem();
    $ns->notifyMembershipActivated(
        $user_id,
        $member_code,
        $plan['name'] . ': ' . $plan['detail'],
        $end_date
    );
} catch (Exception $e) {}

echo json_encode([
    'success'        => true,
    'membership_id'  => $membership_id,
    'member_code'    => $member_code,
    'plan_name'      => $plan['name'],
    'plan_detail'    => $plan['detail'],
    'price'          => $plan['price'],
    'months'         => $plan['months'],
    'free_tickets'   => $plan['free'],
    'start_date'     => $start_date,
    'end_date'       => $end_date,
    'payment_method' => $payment_method,
    'user_name'      => $_SESSION['name'],
    'user_email'     => $_SESSION['email'],
]);
