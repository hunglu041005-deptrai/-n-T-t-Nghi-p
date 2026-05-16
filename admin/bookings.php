<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$isAdminPage = true;
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
        $stmt = $mysqli->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $booking_id);
        $stmt->execute();
        $message = 'Cập nhật trạng thái đặt sân thành công.';
    }
}
$bookings = getAllBookings();
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
                <div>
                    <h2 class="card-title">Quản lý đơn đặt sân</h2>
                    <p class="text-muted mb-0">Xác nhận hoặc hủy đơn đặt sân.</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
            </div>
        </div>
    </div>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo escape($message); ?></div>
<?php endif; ?>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Người đặt</th>
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
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo escape($booking['id']); ?></td>
                            <td><?php echo escape($booking['user_name']); ?> <br><small><?php echo escape($booking['user_email']); ?></small></td>
                            <td><?php echo escape($booking['court_name']); ?></td>
                            <td><?php echo escape($booking['booking_date']); ?></td>
                            <td><?php echo escape(substr($booking['start_time'], 0, 5)); ?> - <?php echo escape(substr($booking['end_time'], 0, 5)); ?></td>
                            <td><?php echo number_format($booking['total_price']); ?> VND</td>
                            <td><?php echo escape($booking['payment_method']); ?> / <?php echo escape($booking['payment_status']); ?></td>
                            <td><span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'warning'); ?>"><?php echo ucfirst(escape($booking['status'])); ?></span></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="booking_id" value="<?php echo escape($booking['id']); ?>">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Chờ</option>
                                        <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Xác nhận</option>
                                        <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Hủy</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
