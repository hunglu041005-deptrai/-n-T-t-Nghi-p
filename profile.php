<?php
require_once __DIR__ . '/includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$userBookings = getUserBookings($_SESSION['user_id']);
$upcomingCount = 0;
foreach ($userBookings as $booking) {
    if ($booking['status'] !== 'cancelled') {
        $upcomingCount++;
    }
}
require_once __DIR__ . '/includes/header.php';
?>
<div class="row gy-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3">Thông tin tài khoản</h2>
                <p class="text-muted">Quản lý hồ sơ cá nhân, xem lịch sử đặt sân và cập nhật thông tin ngay tại đây.</p>
                <dl class="row mt-4">
                    <dt class="col-sm-4">Họ tên</dt>
                    <dd class="col-sm-8"><?php echo escape($_SESSION['name']); ?></dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8"><?php echo escape($_SESSION['email']); ?></dd>
                    <dt class="col-sm-4">Vai trò</dt>
                    <dd class="col-sm-8"><?php echo isAdmin() ? 'Admin' : 'Người dùng'; ?></dd>
                    <dt class="col-sm-4">Lượt đặt sân</dt>
                    <dd class="col-sm-8"><?php echo count($userBookings); ?> đơn</dd>
                    <dt class="col-sm-4">Đơn còn hiệu lực</dt>
                    <dd class="col-sm-8"><?php echo $upcomingCount; ?> đơn</dd>
                </dl>
                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <a href="booking-history.php" class="btn btn-primary">Xem lịch sử đặt sân</a>
                    <a href="logout.php" class="btn btn-outline-secondary">Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-start border-4 border-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Tài khoản nhanh</h5>
                <p class="text-muted">Xem nhanh các chức năng bạn có thể sử dụng.</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Lịch sử đặt sân
                        <span class="badge bg-primary rounded-pill"><?php echo count($userBookings); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Đơn còn hiệu lực
                        <span class="badge bg-success rounded-pill"><?php echo $upcomingCount; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cập nhật hồ sơ
                        <span class="badge bg-secondary rounded-pill">Sắp có</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
