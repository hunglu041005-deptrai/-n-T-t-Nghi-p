<?php
require_once __DIR__ . '/includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$bookings = getUserBookings($_SESSION['user_id']);
require_once __DIR__ . '/includes/header.php';

// Display success/error messages
$success_msg = $_SESSION['booking_success'] ?? $_SESSION['payment_success'] ?? '';
$error_msg = $_SESSION['booking_error'] ?? $_SESSION['payment_error'] ?? '';

if ($success_msg) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo '<strong>✓ Thành công!</strong> ' . escape($success_msg);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['booking_success'], $_SESSION['payment_success']);
}

if ($error_msg) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo '<strong>✗ Lỗi:</strong> ' . escape($error_msg);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['booking_error'], $_SESSION['payment_error']);
}
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-3">Lịch sử đặt sân</h2>
                <?php if (empty($bookings)): ?>
                    <div class="alert alert-info">Bạn chưa có đặt sân nào.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sân</th>
                                    <th>Ngày</th>
                                    <th>Giờ</th>
                                    <th>Giá</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): 
                                    // Determine payment status badge color
                                    $payment_badge = '';
                                    switch(strtolower($booking['payment_status'])) {
                                        case 'paid':
                                            $payment_badge = '<span class="badge bg-success"><i class="fas fa-check"></i> Đã thanh toán</span>';
                                            break;
                                        case 'pending':
                                            $payment_badge = '<span class="badge bg-warning"><i class="fas fa-clock"></i> Chờ xử lý</span>';
                                            break;
                                        case 'unpaid':
                                            $payment_badge = '<span class="badge bg-danger"><i class="fas fa-times"></i> Chưa thanh toán</span>';
                                            break;
                                        case 'failed':
                                            $payment_badge = '<span class="badge bg-danger"><i class="fas fa-exclamation"></i> Thất bại</span>';
                                            break;
                                        default:
                                            $payment_badge = '<span class="badge bg-secondary">' . ucfirst($booking['payment_status']) . '</span>';
                                    }
                                    
                                    // Determine booking status badge
                                    $booking_badge_class = $booking['status'] === 'confirmed' ? 'success' : 
                                                          ($booking['status'] === 'cancelled' ? 'danger' : 'warning');
                                    $status_icon = $booking['status'] === 'confirmed' ? '<i class="fas fa-check-circle"></i>' :
                                                  ($booking['status'] === 'pending' ? '<i class="fas fa-hourglass"></i>' : '');
                                ?>
                                    <tr>
                                        <td><strong>#<?php echo escape($booking['id']); ?></strong></td>
                                        <td>
                                            <strong><?php echo escape($booking['court_name']); ?></strong><br>
                                            <small class="text-muted">📍 <?php echo escape($booking['location']); ?></small>
                                        </td>
                                        <td><?php echo escape($booking['booking_date']); ?></td>
                                        <td><?php echo escape(substr($booking['start_time'], 0, 5)); ?> - <?php echo escape(substr($booking['end_time'], 0, 5)); ?></td>
                                        <td><strong><?php echo number_format($booking['total_price']); ?> VND</strong></td>
                                        <td><?php echo $payment_badge; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $booking_badge_class; ?>">
                                                <?php echo $status_icon; ?> <?php echo ucfirst(escape($booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            // Show payment button if needed
                                            if (in_array(strtolower($booking['payment_status']), ['unpaid', 'failed'])):
                                            ?>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        💳 Thanh toán
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="initiatePayment(<?php echo $booking['id']; ?>, 'vnpay')">VNPay</a></li>
                                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="initiatePayment(<?php echo $booking['id']; ?>, 'momo')">MoMo</a></li>
                                                    </ul>
                                                </div>
                                            <?php 
                                            else:
                                                echo '<small class="text-muted">—</small>';
                                            endif; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Payment Form -->
<form id="paymentForm" method="POST" style="display: none;">
    <input type="hidden" id="bookingId" name="booking_id">
    <input type="hidden" id="paymentMethod" name="payment_method" value="vnpay">
</form>

<script>
function initiatePayment(bookingId, method) {
    document.getElementById('bookingId').value = bookingId;
    document.getElementById('paymentMethod').value = method;
    
    if (method === 'vnpay') {
        // Redirect to payment processing
        window.location.href = 'payment-processing.php?booking_id=' + bookingId + '&method=vnpay';
    } else if (method === 'momo') {
        window.location.href = 'payment-processing.php?booking_id=' + bookingId + '&method=momo';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
