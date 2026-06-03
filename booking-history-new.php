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
    unset($_SESSION['booking_success'], $_SESSION['payment_success']);
}

if ($error_msg) {
    unset($_SESSION['booking_error'], $_SESSION['payment_error']);
}
?>

<style>
/* Enhanced Booking History Styles */
.history-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.history-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.booking-item {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.booking-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.booking-item:hover::before {
    left: 100%;
}

.booking-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.booking-id {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9em;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.85em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-confirmed {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.status-pending {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}

.status-cancelled {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.payment-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8em;
}

.payment-paid {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.payment-unpaid {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.payment-pending {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.booking-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    align-items: center;
}

.detail-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    font-size: 1.1em;
}

.icon-court {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.icon-date {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.icon-time {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.icon-price {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.detail-content h6 {
    margin: 0;
    font-weight: 600;
    color: #495057;
}

.detail-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9em;
}

.booking-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-enhanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 25px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    font-size: 0.85em;
}

.btn-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-success-enhanced {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-success-enhanced:hover {
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(108, 117, 125, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: #6c757d;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6c757d;
    font-weight: 500;
}

.filter-tabs {
    background: white;
    border-radius: 15px;
    padding: 0.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}

.filter-tab {
    background: transparent;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    color: #6c757d;
    transition: all 0.3s ease;
    margin: 0 0.25rem;
}

.filter-tab.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<div class="history-container">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-4">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <i class="fas fa-history me-3"></i>
                    Lịch sử đặt sân
                </h1>
                <p class="lead text-white-50">Quản lý và theo dõi các booking của bạn</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_msg): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="alert alert-success border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-2x"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">Thành công!</h6>
                            <p class="mb-0"><?php echo escape($success_msg); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="alert alert-danger border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">Có lỗi xảy ra!</h6>
                            <p class="mb-0"><?php echo escape($error_msg); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="history-card">
                    <div class="p-4">
                        <?php if (empty($bookings)): ?>
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Chưa có booking nào</h4>
                            <p class="mb-4">Bạn chưa đặt sân nào. Hãy bắt đầu đặt sân để trải nghiệm dịch vụ của chúng tôi!</p>
                            <a href="booking-online-new.php" class="btn btn-enhanced btn-lg">
                                <i class="fas fa-plus me-2"></i>
                                Đặt sân ngay
                            </a>
                        </div>
                        <?php else: ?>
                        
                        <!-- Statistics -->
                        <div class="stats-grid">
                            <?php
                            $totalBookings = count($bookings);
                            $confirmedBookings = count(array_filter($bookings, function($b) { return $b['status'] === 'confirmed'; }));
                            $totalSpent = array_sum(array_column(array_filter($bookings, function($b) { return $b['status'] === 'confirmed'; }), 'total_price'));
                            $upcomingBookings = count(array_filter($bookings, function($b) { 
                                return $b['status'] === 'confirmed' && $b['booking_date'] >= date('Y-m-d'); 
                            }));
                            ?>
                            
                            <div class="stat-card">
                                <div class="stat-number text-primary"><?php echo $totalBookings; ?></div>
                                <div class="stat-label">Tổng booking</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number text-success"><?php echo $confirmedBookings; ?></div>
                                <div class="stat-label">Đã xác nhận</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number text-info"><?php echo $upcomingBookings; ?></div>
                                <div class="stat-label">Sắp tới</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number text-warning"><?php echo number_format($totalSpent); ?>đ</div>
                                <div class="stat-label">Tổng chi tiêu</div>
                            </div>
                        </div>

                        <!-- Filter Tabs -->
                        <div class="filter-tabs">
                            <button class="filter-tab active" data-filter="all">
                                <i class="fas fa-list me-2"></i>Tất cả
                            </button>
                            <button class="filter-tab" data-filter="confirmed">
                                <i class="fas fa-check me-2"></i>Đã xác nhận
                            </button>
                            <button class="filter-tab" data-filter="pending">
                                <i class="fas fa-clock me-2"></i>Chờ xử lý
                            </button>
                            <button class="filter-tab" data-filter="upcoming">
                                <i class="fas fa-calendar-alt me-2"></i>Sắp tới
                            </button>
                        </div>

                        <!-- Bookings List -->
                        <div id="bookingsList">
                            <?php foreach ($bookings as $booking): 
                                $isUpcoming = $booking['booking_date'] >= date('Y-m-d');
                                $statusClass = $booking['status'] === 'confirmed' ? 'confirmed' : 
                                              ($booking['status'] === 'cancelled' ? 'cancelled' : 'pending');
                                $paymentClass = $booking['payment_status'] === 'paid' ? 'paid' : 
                                               ($booking['payment_status'] === 'pending' ? 'pending' : 'unpaid');
                            ?>
                            <div class="booking-item fade-in" 
                                 data-status="<?php echo $booking['status']; ?>"
                                 data-upcoming="<?php echo $isUpcoming ? 'true' : 'false'; ?>">
                                
                                <!-- Header -->
                                <div class="booking-header">
                                    <div class="booking-id">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        #<?php echo $booking['id']; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="status-badge status-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                        <span class="payment-badge payment-<?php echo $paymentClass; ?>">
                                            <?php echo ucfirst($booking['payment_status']); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="booking-details">
                                    <div class="detail-item">
                                        <div class="detail-icon icon-court">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="detail-content">
                                            <h6><?php echo escape($booking['court_name']); ?></h6>
                                            <p><?php echo escape($booking['location']); ?></p>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon icon-date">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div class="detail-content">
                                            <h6><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></h6>
                                            <p><?php echo date('l', strtotime($booking['booking_date'])); ?></p>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon icon-time">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="detail-content">
                                            <h6><?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?></h6>
                                            <p>
                                                <?php 
                                                $start = new DateTime($booking['start_time']);
                                                $end = new DateTime($booking['end_time']);
                                                $duration = $start->diff($end);
                                                echo $duration->h . ' giờ';
                                                ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon icon-price">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="detail-content">
                                            <h6><?php echo number_format($booking['total_price']); ?>đ</h6>
                                            <p><?php echo ucfirst($booking['payment_method']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="booking-actions">
                                    <?php if ($booking['payment_status'] === 'unpaid' && $booking['status'] === 'confirmed'): ?>
                                    <button class="btn btn-success-enhanced" onclick="initiatePayment(<?php echo $booking['id']; ?>)">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Thanh toán
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($isUpcoming && $booking['status'] === 'confirmed'): ?>
                                    <button class="btn btn-enhanced" onclick="viewBookingDetails(<?php echo $booking['id']; ?>)">
                                        <i class="fas fa-eye me-2"></i>
                                        Chi tiết
                                    </button>
                                    <?php endif; ?>
                                    
                                    <button class="btn btn-outline-secondary" onclick="downloadReceipt(<?php echo $booking['id']; ?>)">
                                        <i class="fas fa-download me-2"></i>
                                        Hóa đơn
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- New Booking Button -->
                        <div class="text-center mt-4">
                            <a href="booking-online-new.php" class="btn btn-enhanced btn-lg">
                                <i class="fas fa-plus me-2"></i>
                                Đặt sân mới
                            </a>
                        </div>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter bookings
            const filter = this.dataset.filter;
            filterBookings(filter);
        });
    });
    
    function filterBookings(filter) {
        const bookings = document.querySelectorAll('.booking-item');
        
        bookings.forEach(booking => {
            let show = false;
            
            switch(filter) {
                case 'all':
                    show = true;
                    break;
                case 'confirmed':
                    show = booking.dataset.status === 'confirmed';
                    break;
                case 'pending':
                    show = booking.dataset.status === 'pending';
                    break;
                case 'upcoming':
                    show = booking.dataset.upcoming === 'true' && booking.dataset.status === 'confirmed';
                    break;
            }
            
            if (show) {
                booking.style.display = 'block';
                booking.classList.add('fade-in');
            } else {
                booking.style.display = 'none';
            }
        });
    }
});

function initiatePayment(bookingId) {
    // Redirect to payment page
    window.location.href = `payment-processing.php?booking_id=${bookingId}&method=vnpay`;
}

function viewBookingDetails(bookingId) {
    // Show booking details modal or redirect
    alert('Xem chi tiết booking #' + bookingId);
}

function downloadReceipt(bookingId) {
    // Download receipt
    alert('Tải hóa đơn booking #' + bookingId);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>