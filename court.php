<?php
require_once __DIR__ . '/includes/functions.php';
$court_id = intval($_GET['id'] ?? 0);
$court = getCourtById($court_id);
if (!$court) {
    header('Location: index.php');
    exit;
}
$date = $_GET['date'] ?? date('Y-m-d');
$today = date('Y-m-d');
$errorMessage = $_SESSION['booking_error'] ?? '';
unset($_SESSION['booking_error']);
$availableSlots = [];
$existingBookings = getCourtAvailability($court_id, $date);
$hours = range(6, 21);
foreach ($hours as $hour) {
    $slotStart = sprintf('%02d:00:00', $hour);
    $slotEnd = sprintf('%02d:00:00', $hour + 1);
    $available = true;
    foreach ($existingBookings as $booking) {
        if (!($slotEnd <= $booking['start_time'] || $slotStart >= $booking['end_time'])) {
            $available = false;
            break;
        }
    }
    $availableSlots[] = [
        'start' => substr($slotStart, 0, 5),
        'end' => substr($slotEnd, 0, 5),
        'available' => $available,
    ];
}
require_once __DIR__ . '/includes/header.php';
?>
<div class="row gy-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <img src="<?php echo escape($court['cover_image']); ?>" class="card-img-top" alt="<?php echo escape($court['name']); ?>">
            <div class="card-body">
                <h2 class="card-title"><?php echo escape($court['name']); ?></h2>
                <p class="text-muted mb-1"><strong>Khu vực:</strong> <?php echo escape($court['location']); ?></p>
                <p class="text-muted mb-1"><strong>Giá mỗi giờ:</strong> <?php echo number_format($court['price_per_hour']); ?> VND</p>
                <p><?php echo nl2br(escape($court['description'])); ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="h5 mb-3">Đặt sân</h3>
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger"><?php echo escape($errorMessage); ?></div>
                <?php endif; ?>
                <?php if (!isLoggedIn()): ?>
                    <div class="alert alert-warning">Bạn cần <a href="login.php">đăng nhập</a> để đặt sân.</div>
                <?php endif; ?>
                <form action="book.php" method="post">
                    <input type="hidden" name="court_id" value="<?php echo escape($court['id']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Chọn ngày</label>
                        <input type="date" name="booking_date" class="form-control" min="<?php echo $today; ?>" value="<?php echo escape($date); ?>" <?php echo !isLoggedIn() ? 'disabled' : 'required'; ?> >
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn khung giờ</label>
                        <select name="start_time" class="form-select" required>
                            <option value="">Chọn giờ bắt đầu</option>
                            <?php foreach ($availableSlots as $slot): ?>
                                <?php if ($slot['available']): ?>
                                    <option value="<?php echo escape($slot['start']); ?>"><?php echo escape($slot['start']); ?> - <?php echo escape($slot['end']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số giờ</label>
                        <select name="duration" class="form-select" required>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> giờ</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-select" <?php echo !isLoggedIn() ? 'disabled' : 'required'; ?>>
                            <option value="cash">Tiền mặt</option>
                            <option value="momo">MoMo</option>
                            <option value="vnpay">VNPay</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>Đặt sân</button>
                </form>
                <div class="mt-4">
                    <h5 class="mb-2">Khung giờ trống hôm nay</h5>
                    <div class="list-group">
                        <?php foreach ($availableSlots as $slot): ?>
                            <div class="list-group-item <?php echo $slot['available'] ? 'list-group-item-light' : 'list-group-item-danger'; ?>">
                                <?php echo escape($slot['start']); ?> - <?php echo escape($slot['end']); ?>
                                <?php if ($slot['available']): ?>
                                    <span class="badge bg-success float-end">Trống</span>
                                <?php else: ?>
                                    <span class="badge bg-danger float-end">Đã đặt</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
