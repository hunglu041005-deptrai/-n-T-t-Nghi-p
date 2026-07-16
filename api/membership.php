<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';

// ===== AUTO MIGRATION =====
$mysqli->query("CREATE TABLE IF NOT EXISTS memberships (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT NOT NULL,
    plan_id          INT NOT NULL,
    plan_name        VARCHAR(100),
    plan_detail      VARCHAR(100),
    price            INT,
    months           INT,
    free_tickets     INT DEFAULT 0,
    tickets_used     INT DEFAULT 0,
    payment_method   VARCHAR(30),
    payment_status   VARCHAR(30) DEFAULT 'pending',
    status           VARCHAR(30) DEFAULT 'active',
    member_code      VARCHAR(30) UNIQUE,
    start_date       DATE,
    end_date         DATE,
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user   (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS membership_ticket_logs (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    membership_id   INT NOT NULL,
    user_id         INT NOT NULL,
    booking_id      INT DEFAULT NULL,
    action          VARCHAR(20) NOT NULL DEFAULT 'use',
    tickets_delta   INT NOT NULL,
    note            VARCHAR(200),
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_membership (membership_id),
    INDEX idx_user       (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Safe column additions — kiểm tra trước khi ALTER
$col = $mysqli->query("SHOW COLUMNS FROM memberships LIKE 'tickets_used'");
if ($col && $col->num_rows === 0) {
    $mysqli->query("ALTER TABLE memberships ADD COLUMN tickets_used INT NOT NULL DEFAULT 0");
}
$col2 = $mysqli->query("SHOW COLUMNS FROM memberships LIKE 'updated_at'");
if ($col2 && $col2->num_rows === 0) {
    $mysqli->query("ALTER TABLE memberships ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
}

// ===== ROUTE DISPATCH =====
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// GET actions (không cần login)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'status' && isLoggedIn()) {
        // Trả về trạng thái membership của user hiện tại
        $m = getActiveMembership((int)$_SESSION['user_id']);
        echo json_encode([
            'success'    => true,
            'has_active' => (bool)$m,
            'membership' => $m ? [
                'id'               => $m['id'],
                'member_code'      => $m['member_code'],
                'plan_name'        => $m['plan_name'],
                'plan_detail'      => $m['plan_detail'],
                'months'           => $m['months'],
                'free_tickets'     => $m['free_tickets'],
                'tickets_used'     => $m['tickets_used'] ?? 0,
                'tickets_remaining'=> getMembershipTicketsRemaining($m),
                'member_price'     => getMemberPrice(),
                'start_date'       => $m['start_date'],
                'end_date'         => $m['end_date'],
                'status'           => $m['status'],
                'payment_status'   => $m['payment_status'],
            ] : null,
        ]);
        exit;
    }

    if ($action === 'ticket_logs' && isLoggedIn()) {
        $m = getActiveMembership((int)$_SESSION['user_id']);
        if (!$m) { echo json_encode(['success'=>true,'logs',[]]); exit; }
        $logs = getMembershipTicketLogs((int)$m['id']);
        echo json_encode(['success' => true, 'logs' => $logs]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Unknown action']);
    exit;
}

// POST actions (cần login)
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
    1 => ['name'=>'COMBO CHIỀU 14H–17H', 'detail'=>'10 VÉ TẶNG 1 VÉ',  'price'=>720000,  'months'=>3,  'free'=>11],
    2 => ['name'=>'COMBO CHIỀU 14H–17H', 'detail'=>'20 VÉ TẶNG 2 VÉ',  'price'=>1440000, 'months'=>6,  'free'=>22],
    3 => ['name'=>'COMBO TỐI 20H–22H',   'detail'=>'20 VÉ TẶNG 2 VÉ',  'price'=>1440000, 'months'=>9,  'free'=>22],
    4 => ['name'=>'COMBO TỐI 20H–22H',   'detail'=>'30 VÉ TẶNG 3 VÉ',  'price'=>2160000, 'months'=>12, 'free'=>33],
    5 => ['name'=>'COMBO CHIỀU 15H–18H', 'detail'=>'10 VÉ TẶNG 1 VÉ',  'price'=>720000,  'months'=>3,  'free'=>11],
    6 => ['name'=>'COMBO CHIỀU 15H–18H', 'detail'=>'20 VÉ TẶNG 2 VÉ',  'price'=>1440000, 'months'=>6,  'free'=>22],
];

// ── Đăng ký gói mới ──
if ($action === '' || $action === 'register') {
    $plan_id        = intval($_POST['plan_id'] ?? 0);
    $payment_method = strtolower(trim($_POST['payment_method'] ?? 'cash'));

    if (!isset($plans[$plan_id])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Gói không hợp lệ.']);
        exit;
    }

    $plan    = $plans[$plan_id];
    $user_id = (int) $_SESSION['user_id'];

    // Kiểm tra đã có gói active chưa
    $existing = getActiveMembership($user_id);
    if ($existing) {
        echo json_encode([
            'success'        => false,
            'error'          => 'Bạn đang có gói hội viên active (còn hạn đến ' . date('d/m/Y', strtotime($existing['end_date'])) . '). Hãy chờ hết hạn hoặc huỷ gói hiện tại trước.',
            'existing'       => true,
            'existing_code'  => $existing['member_code'],
            'existing_end'   => $existing['end_date'],
        ]);
        exit;
    }

    $start_date = date('Y-m-d');
    $end_date   = date('Y-m-d', strtotime("+{$plan['months']} months"));

    // Sinh mã thẻ hội viên duy nhất
    do {
        $member_code = 'HV' . date('Y') . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $check = $mysqli->prepare('SELECT id FROM memberships WHERE member_code = ?');
        $check->bind_param('s', $member_code);
        $check->execute();
        $check->store_result();
        $exists = $check->num_rows > 0;
        $check->close();
    } while ($exists);

    // Cash = active ngay, bank/momo = pending_payment (chờ webhook xác nhận thật)
    $payment_status = ($payment_method === 'cash') ? 'paid'    : 'pending';
    $status         = ($payment_method === 'cash') ? 'active'  : 'pending_payment';

    // Nếu cash: thông báo ngay; nếu chuyển khoản: không thông báo vội
    $send_notify = ($payment_method === 'cash');

    $stmt = $mysqli->prepare(
        'INSERT INTO memberships
         (user_id, plan_id, plan_name, plan_detail, price, months, free_tickets, tickets_used,
          payment_method, payment_status, status, member_code, start_date, end_date)
         VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'iissiiiissssss',
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

    // Gửi thông báo chỉ khi cash (chuyển khoản chờ webhook)
    if ($send_notify) {
        try {
            require_once __DIR__ . '/../includes/notification-system.php';
            $ns = new NotificationSystem();
            $ns->notifyMembershipActivated($user_id, $member_code, $plan['name'] . ': ' . $plan['detail'], $end_date);
        } catch (Exception $e) {}
    }

    echo json_encode([
        'success'           => true,
        'membership_id'     => $membership_id,
        'member_code'       => $member_code,
        'plan_name'         => $plan['name'],
        'plan_detail'       => $plan['detail'],
        'price'             => $plan['price'],
        'months'            => $plan['months'],
        'free_tickets'      => $plan['free'],
        'tickets_remaining' => $plan['free'],
        'member_price'      => getMemberPrice(),
        'start_date'        => $start_date,
        'end_date'          => $end_date,
        'payment_method'    => $payment_method,
        'payment_status'    => $payment_status,
        'user_name'         => $_SESSION['name'],
        'user_email'        => $_SESSION['email'] ?? '',
    ]);
    exit;
}

// ── Dùng vé hội viên ──
if ($action === 'use_ticket') {
    $membership_id = intval($_POST['membership_id'] ?? 0);
    $booking_id    = intval($_POST['booking_id']    ?? 0) ?: null;
    $note          = trim($_POST['note'] ?? 'Đặt sân');

    $ok = useMemberTicket($membership_id, (int)$_SESSION['user_id'], $booking_id, $note);
    echo json_encode(['success' => $ok, 'error' => $ok ? null : 'Không thể dùng vé. Hết vé hoặc gói hết hạn.']);
    exit;
}

// ── Hoàn vé ──
if ($action === 'refund_ticket') {
    $membership_id = intval($_POST['membership_id'] ?? 0);
    $booking_id    = intval($_POST['booking_id']    ?? 0) ?: null;

    $ok = refundMemberTicket($membership_id, (int)$_SESSION['user_id'], $booking_id);
    echo json_encode(['success' => $ok]);
    exit;
}

// ── Huỷ gói ──
if ($action === 'cancel') {
    $membership_id = intval($_POST['membership_id'] ?? 0);
    $user_id = (int)$_SESSION['user_id'];

    $upd = $mysqli->prepare(
        'UPDATE memberships SET status = "cancelled" WHERE id = ? AND user_id = ? AND status = "active"'
    );
    $upd->bind_param('ii', $membership_id, $user_id);
    $upd->execute();
    $affected = $upd->affected_rows;
    $upd->close();

    echo json_encode(['success' => $affected > 0, 'error' => $affected > 0 ? null : 'Không tìm thấy gói active.']);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Unknown action']);
