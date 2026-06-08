<?php
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$bookings    = getUserBookings($_SESSION['user_id']);
$success_msg = $_SESSION['booking_success'] ?? '';
$error_msg   = $_SESSION['booking_error']   ?? '';
unset($_SESSION['booking_success'], $_SESSION['booking_error']);

require_once __DIR__ . '/includes/header.php';
?>

<style>
.history-wrap { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: .6rem;
}

.booking-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(0,0,0,.07);
    margin-bottom: 1.2rem;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    border: 1px solid #f0f0f0;
}
.booking-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,.12);
}

.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .9rem 1.4rem;
    background: linear-gradient(135deg,#667eea,#764ba2);
    color: #fff;
}
.booking-id { font-weight: 700; font-size: 1rem; }
.booking-date-created { font-size: .8rem; opacity: .85; }

.booking-card-body {
    padding: 1.2rem 1.4rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1rem;
}

.info-item { display: flex; flex-direction: column; gap: .2rem; }
.info-label { font-size: .75rem; color: #888; text-transform: uppercase; letter-spacing: .5px; }
.info-value { font-weight: 600; color: #333; font-size: .95rem; }

.booking-card-footer {
    padding: .8rem 1.4rem;
    background: #fafafa;
    border-top: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .5rem;
}

.badge-status {
    padding: .35rem .9rem;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 600;
}
.badge-confirmed { background: #d4edda; color: #155724; }
.badge-pending   { background: #fff3cd; color: #856404; }
.badge-cancelled { background: #f8d7da; color: #721c24; }

.badge-payment {
    padding: .3rem .8rem;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 600;
}
.badge-paid   { background: #d4edda; color: #155724; }
.badge-unpaid { background: #f8d7da; color: #721c24; }
.badge-ppending { background: #fff3cd; color: #856404; }

.empty-state {
    text-align: center;
    padding: 4rem 1rem;
    color: #888;
}
.empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: .3; }
.empty-state h4 { font-weight: 600; margin-bottom: .5rem; }

.btn-pay {
    background: linear-gradient(135deg,#28a745,#20c997);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .4rem 1rem;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .2s;
}
.btn-pay:hover { opacity: .85; color: #fff; }

.summary-bar {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.summary-item {
    background: #fff;
    border-radius: 12px;
    padding: .8rem 1.4rem;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    flex: 1;
    min-width: 130px;
    text-align: center;
}
.summary-item .num { font-size: 1.6rem; font-weight: 700; }
.summary-item .lbl { font-size: .78rem; color: #888; }
</style>

<div class="history-wrap">
    <div class="page-title">
        <i class="fas fa-history text-primary"></i>
        Lịch sử đặt sân
    </div>

    <?php if ($success_msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo escape($success_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo escape($error_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
    <div class="empty-state">
        <i class="fas fa-calendar-times d-block"></i>
        <h4>Bạn chưa có đặt sân nào</h4>
        <p>Hãy đặt sân ngay để trải nghiệm dịch vụ!</p>
        <a href="booking-online.php" class="btn btn-primary mt-2">
            <i class="fas fa-plus me-2"></i>Đặt sân ngay
        </a>
    </div>
    <?php else: ?>

    <!-- Summary bar -->
    <?php
    $total     = count($bookings);
    $confirmed = count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed'));
    $upcoming  = count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed' && $b['booking_date'] >= date('Y-m-d')));
    $spent     = array_sum(array_column(array_filter($bookings, fn($b) => $b['status'] === 'confirmed'), 'total_price'));
    ?>
    <div class="summary-bar">
        <div class="summary-item">
            <div class="num text-primary"><?php echo $total; ?></div>
            <div class="lbl">Tổng booking</div>
        </div>
        <div class="summary-item">
            <div class="num text-success"><?php echo $confirmed; ?></div>
            <div class="lbl">Đã xác nhận</div>
        </div>
        <div class="summary-item">
            <div class="num text-info"><?php echo $upcoming; ?></div>
            <div class="lbl">Sắp tới</div>
        </div>
        <div class="summary-item">
            <div class="num text-warning" style="font-size:1.1rem"><?php echo number_format($spent); ?>đ</div>
            <div class="lbl">Tổng chi tiêu</div>
        </div>
    </div>

    <!-- Booking cards -->
    <?php foreach ($bookings as $b):
        $statusClass  = ['confirmed'=>'badge-confirmed','pending'=>'badge-pending','cancelled'=>'badge-cancelled'][$b['status']] ?? 'badge-pending';
        $statusLabel  = ['confirmed'=>'✓ Đã xác nhận','pending'=>'⏳ Chờ xử lý','cancelled'=>'✗ Đã hủy'][$b['status']] ?? $b['status'];
        $payClass     = ['paid'=>'badge-paid','unpaid'=>'badge-unpaid','pending'=>'badge-ppending','failed'=>'badge-unpaid'][$b['payment_status']] ?? 'badge-ppending';
        $payLabel     = ['paid'=>'Đã thanh toán','unpaid'=>'Chưa thanh toán','pending'=>'Chờ xử lý','failed'=>'Thất bại'][$b['payment_status']] ?? $b['payment_status'];
    ?>
    <div class="booking-card">
        <div class="booking-card-header">
            <span class="booking-id"><i class="fas fa-ticket-alt me-2"></i>Booking #<?php echo $b['id']; ?></span>
            <span class="booking-date-created">Đặt lúc: <?php echo date('d/m/Y H:i', strtotime($b['created_at'])); ?></span>
        </div>

        <div class="booking-card-body">
            <div class="info-item">
                <span class="info-label"><i class="fas fa-map-marker-alt me-1"></i>Sân</span>
                <span class="info-value"><?php echo escape($b['court_name']); ?></span>
                <small class="text-muted"><?php echo escape($b['location'] ?? ''); ?></small>
            </div>
            <div class="info-item">
                <span class="info-label"><i class="fas fa-calendar me-1"></i>Ngày</span>
                <span class="info-value"><?php echo date('d/m/Y', strtotime($b['booking_date'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label"><i class="fas fa-clock me-1"></i>Giờ</span>
                <span class="info-value"><?php echo substr($b['start_time'],0,5); ?> – <?php echo substr($b['end_time'],0,5); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label"><i class="fas fa-money-bill me-1"></i>Tổng tiền</span>
                <span class="info-value text-success"><?php echo number_format($b['total_price']); ?>đ</span>
                <small class="text-muted"><?php echo strtoupper($b['payment_method']); ?></small>
            </div>
        </div>

        <div class="booking-card-footer">
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                <span class="badge-payment <?php echo $payClass; ?>"><?php echo $payLabel; ?></span>
            </div>
            <?php if (in_array($b['payment_status'], ['unpaid','failed']) && $b['status'] === 'confirmed'): ?>
            <button class="btn-pay" onclick="window.location.href='payment-processing.php?booking_id=<?php echo $b['id']; ?>&method=vnpay'">
                <i class="fas fa-credit-card me-1"></i>Thanh toán ngay
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="text-center mt-3">
        <a href="booking-online.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Đặt sân mới
        </a>
    </div>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
