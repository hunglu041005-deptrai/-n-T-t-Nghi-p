<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.demo-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.demo-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    margin-bottom: 2rem;
}

.demo-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 2rem;
    text-align: center;
}

.demo-content {
    padding: 2rem;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.feature-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    text-align: center;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
}

.icon-booking {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.icon-history {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.icon-payment {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.btn-enhanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    text-decoration: none;
    display: inline-block;
}

.btn-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.btn-success-enhanced {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-success-enhanced:hover {
    box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
}

.btn-warning-enhanced {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
}

.btn-warning-enhanced:hover {
    box-shadow: 0 12px 35px rgba(255, 193, 7, 0.4);
}

.improvements-list {
    background: rgba(40, 167, 69, 0.1);
    border-radius: 15px;
    padding: 2rem;
    margin: 2rem 0;
}

.improvement-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.improvement-item:last-child {
    margin-bottom: 0;
}

.improvement-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.1em;
}

.comparison-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin: 2rem 0;
}

.comparison-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.comparison-card.old {
    border-left: 4px solid #dc3545;
}

.comparison-card.new {
    border-left: 4px solid #28a745;
}

.comparison-title {
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.comparison-title.old {
    color: #dc3545;
}

.comparison-title.new {
    color: #28a745;
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

<div class="demo-container">
    <div class="container">
        <!-- Main Demo Card -->
        <div class="demo-card fade-in">
            <div class="demo-header">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-magic me-3"></i>
                    Enhanced Booking System
                </h1>
                <p class="lead mb-0">Hệ thống đặt sân mới với giao diện mượt mà và chuyên nghiệp</p>
            </div>

            <div class="demo-content">
                <!-- Feature Grid -->
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon icon-booking">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Đặt sân trực tuyến</h4>
                        <p class="text-muted mb-4">Giao diện mới với step-by-step wizard, real-time availability, và smooth animations</p>
                        <a href="booking-online-new.php" class="btn btn-enhanced">
                            <i class="fas fa-arrow-right me-2"></i>
                            Trải nghiệm ngay
                        </a>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon icon-history">
                            <i class="fas fa-history"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Lịch sử đặt sân</h4>
                        <p class="text-muted mb-4">Dashboard với statistics, filter tabs, và enhanced booking cards</p>
                        <a href="booking-history-new.php" class="btn btn-success-enhanced">
                            <i class="fas fa-eye me-2"></i>
                            Xem demo
                        </a>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon icon-payment">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Thanh toán</h4>
                        <p class="text-muted mb-4">Payment processing với security indicators và smooth transitions</p>
                        <a href="payment-processing-new.php?booking_id=1&method=vnpay" class="btn btn-warning-enhanced">
                            <i class="fas fa-lock me-2"></i>
                            Xem thanh toán
                        </a>
                    </div>
                </div>

                <!-- Improvements List -->
                <div class="improvements-list">
                    <h4 class="fw-bold mb-4 text-center">
                        <i class="fas fa-star text-warning me-2"></i>
                        Cải tiến chính
                    </h4>
                    
                    <div class="improvement-item">
                        <div class="improvement-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Giao diện mới hoàn toàn</h6>
                            <p class="mb-0 text-muted">Glassmorphism design với gradient backgrounds và smooth animations</p>
                        </div>
                    </div>

                    <div class="improvement-item">
                        <div class="improvement-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Responsive Design</h6>
                            <p class="mb-0 text-muted">Tối ưu cho tất cả thiết bị từ mobile đến desktop</p>
                        </div>
                    </div>

                    <div class="improvement-item">
                        <div class="improvement-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Performance Enhancement</h6>
                            <p class="mb-0 text-muted">AJAX loading, real-time updates, và optimized animations</p>
                        </div>
                    </div>

                    <div class="improvement-item">
                        <div class="improvement-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">User Experience</h6>
                            <p class="mb-0 text-muted">Intuitive navigation, clear feedback, và error handling</p>
                        </div>
                    </div>

                    <div class="improvement-item">
                        <div class="improvement-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Security & Trust</h6>
                            <p class="mb-0 text-muted">Visual security indicators và trusted payment methods</p>
                        </div>
                    </div>
                </div>

                <!-- Comparison -->
                <h4 class="fw-bold mb-4 text-center">So sánh Before & After</h4>
                <div class="comparison-grid">
                    <div class="comparison-card old">
                        <div class="comparison-title old">
                            <i class="fas fa-times-circle me-2"></i>
                            Trước khi cải tiến
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-minus text-danger me-2"></i>Giao diện cơ bản</li>
                            <li class="mb-2"><i class="fas fa-minus text-danger me-2"></i>Không có animations</li>
                            <li class="mb-2"><i class="fas fa-minus text-danger me-2"></i>Form truyền thống</li>
                            <li class="mb-2"><i class="fas fa-minus text-danger me-2"></i>Thiếu feedback</li>
                            <li class="mb-2"><i class="fas fa-minus text-danger me-2"></i>UX chưa tối ưu</li>
                        </ul>
                    </div>

                    <div class="comparison-card new">
                        <div class="comparison-title new">
                            <i class="fas fa-check-circle me-2"></i>
                            Sau khi cải tiến
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-plus text-success me-2"></i>Glassmorphism design</li>
                            <li class="mb-2"><i class="fas fa-plus text-success me-2"></i>Smooth animations</li>
                            <li class="mb-2"><i class="fas fa-plus text-success me-2"></i>Step-by-step wizard</li>
                            <li class="mb-2"><i class="fas fa-plus text-success me-2"></i>Real-time feedback</li>
                            <li class="mb-2"><i class="fas fa-plus text-success me-2"></i>Enhanced UX/UI</li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Access -->
                <div class="text-center mt-4">
                    <h5 class="fw-bold mb-3">Quick Access</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="quick-login.php" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Quick Login
                        </a>
                        <a href="booking-online-new.php" class="btn btn-enhanced">
                            <i class="fas fa-calendar-check me-2"></i>New Booking
                        </a>
                        <a href="booking-history-new.php" class="btn btn-success-enhanced">
                            <i class="fas fa-history me-2"></i>New History
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>