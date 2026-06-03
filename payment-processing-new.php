<?php
require_once __DIR__ . '/includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$booking_id = $_GET['booking_id'] ?? null;
$method = $_GET['method'] ?? 'vnpay';

if (!$booking_id) {
    header('Location: booking-history-new.php');
    exit;
}

// Lấy thông tin booking
$booking = getBookingById($booking_id);
if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
    header('Location: booking-history-new.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Enhanced Payment Processing Styles */
.payment-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.payment-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.payment-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 2rem;
    text-align: center;
    position: relative;
}

.payment-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
}

.payment-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    position: relative;
    z-index: 1;
}

.booking-summary {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.summary-row:last-child {
    border-bottom: none;
    font-weight: 700;
    font-size: 1.2em;
    color: #28a745;
    background: rgba(40, 167, 69, 0.1);
    margin: 1rem -1rem -1rem;
    padding: 1rem;
    border-radius: 0 0 15px 15px;
}

.payment-methods {
    padding: 2rem;
}

.method-option {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.method-option::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.method-option:hover::before {
    left: 100%;
}

.method-option:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border-color: #667eea;
}

.method-option.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
    transform: scale(1.02);
    box-shadow: 0 15px 35px rgba(40, 167, 69, 0.2);
}

.method-option.selected::after {
    content: '✓';
    position: absolute;
    top: 15px;
    right: 20px;
    background: #28a745;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    animation: checkmark 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes checkmark {
    0% {
        transform: scale(0) rotate(180deg);
        opacity: 0;
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

.method-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.vnpay-icon {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: white;
}

.momo-icon {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: white;
}

.method-details h5 {
    margin: 0 0 0.5rem;
    font-weight: 700;
    color: #495057;
}

.method-details p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9em;
}

.method-features {
    margin-top: 0.75rem;
}

.feature-tag {
    display: inline-block;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    margin-right: 0.5rem;
    margin-top: 0.25rem;
}

.security-info {
    background: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.3);
    border-radius: 15px;
    padding: 1.5rem;
    margin: 2rem;
    text-align: center;
}

.btn-enhanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    font-size: 1.1em;
}

.btn-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-success-enhanced {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-success-enhanced:hover {
    box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
}

.processing-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.processing-content {
    text-align: center;
    max-width: 400px;
    padding: 2rem;
}

.processing-spinner {
    width: 80px;
    height: 80px;
    border: 4px solid #e9ecef;
    border-top: 4px solid #28a745;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 2rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.countdown {
    font-size: 1.2em;
    font-weight: 600;
    color: #28a745;
    margin-top: 1rem;
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

<div class="payment-container">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-4">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <i class="fas fa-credit-card me-3"></i>
                    Thanh toán
                </h1>
                <p class="lead text-white-50">Hoàn tất booking của bạn</p>
            </div>
        </div>

        <!-- Main Payment Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="payment-card fade-in">
                    <!-- Payment Header -->
                    <div class="payment-header">
                        <div class="payment-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="fw-bold mb-2">Thanh toán an toàn</h3>
                        <p class="mb-0 opacity-75">Giao dịch được mã hóa và bảo mật</p>
                    </div>

                    <!-- Booking Summary -->
                    <div class="booking-summary">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-receipt me-2 text-primary"></i>
                            Thông tin booking
                        </h5>
                        
                        <div class="summary-row">
                            <span>Mã booking:</span>
                            <span class="fw-bold">#<?php echo $booking['id']; ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Sân:</span>
                            <span class="fw-bold"><?php echo escape($booking['court_name']); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Ngày:</span>
                            <span class="fw-bold"><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Giờ:</span>
                            <span class="fw-bold"><?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tổng tiền:</span>
                            <span class="fw-bold"><?php echo number_format($booking['total_price']); ?>đ</span>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="payment-methods">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-credit-card me-2 text-primary"></i>
                            Chọn phương thức thanh toán
                        </h5>

                        <div class="method-option <?php echo $method === 'vnpay' ? 'selected' : ''; ?>" data-method="vnpay">
                            <div class="d-flex align-items-center">
                                <div class="method-icon vnpay-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="method-details flex-grow-1">
                                    <h5>VNPay</h5>
                                    <p>Thanh toán qua ngân hàng trực tuyến</p>
                                    <div class="method-features">
                                        <span class="feature-tag">Bảo mật cao</span>
                                        <span class="feature-tag">Xử lý nhanh</span>
                                        <span class="feature-tag">Hỗ trợ 24/7</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>

                        <div class="method-option <?php echo $method === 'momo' ? 'selected' : ''; ?>" data-method="momo">
                            <div class="d-flex align-items-center">
                                <div class="method-icon momo-icon">
                                    <i class="fab fa-cc-mastercard"></i>
                                </div>
                                <div class="method-details flex-grow-1">
                                    <h5>Ví MoMo</h5>
                                    <p>Thanh toán qua ví điện tử MoMo</p>
                                    <div class="method-features">
                                        <span class="feature-tag">Tiện lợi</span>
                                        <span class="feature-tag">Tích điểm</span>
                                        <span class="feature-tag">Khuyến mãi</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="security-info">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-shield-alt text-success me-2 fa-lg"></i>
                            <strong class="text-success">Giao dịch được bảo mật</strong>
                        </div>
                        <p class="mb-0 text-muted">
                            Thông tin thanh toán của bạn được mã hóa SSL 256-bit và tuân thủ tiêu chuẩn bảo mật PCI DSS
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center p-4">
                        <button class="btn btn-success-enhanced btn-lg me-3" id="processPayment">
                            <i class="fas fa-lock me-2"></i>
                            Thanh toán <?php echo number_format($booking['total_price']); ?>đ
                        </button>
                        <a href="booking-history-new.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing Overlay -->
<div class="processing-overlay d-none" id="processingOverlay">
    <div class="processing-content">
        <div class="processing-spinner"></div>
        <h4 class="fw-bold text-success mb-2">Đang xử lý thanh toán</h4>
        <p class="text-muted mb-3">Vui lòng không đóng trang này</p>
        <div class="countdown" id="countdown">Chuyển hướng trong <span id="countdownNumber">5</span>s</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedMethod = '<?php echo $method; ?>';
    
    // Payment method selection
    document.querySelectorAll('.method-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.method-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Select this option
            this.classList.add('selected');
            selectedMethod = this.dataset.method;
            
            console.log('Payment method selected:', selectedMethod);
        });
    });
    
    // Process payment
    document.getElementById('processPayment').addEventListener('click', function() {
        if (!selectedMethod) {
            alert('Vui lòng chọn phương thức thanh toán');
            return;
        }
        
        // Show processing overlay
        document.getElementById('processingOverlay').classList.remove('d-none');
        
        // Simulate payment processing
        let countdown = 5;
        const countdownElement = document.getElementById('countdownNumber');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                
                // Redirect based on payment method
                if (selectedMethod === 'vnpay') {
                    // Simulate VNPay redirect
                    window.location.href = `vnpay-redirect.php?booking_id=<?php echo $booking_id; ?>&amount=<?php echo $booking['total_price']; ?>`;
                } else if (selectedMethod === 'momo') {
                    // Simulate MoMo redirect
                    window.location.href = `momo-redirect.php?booking_id=<?php echo $booking_id; ?>&amount=<?php echo $booking['total_price']; ?>`;
                }
            }
        }, 1000);
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>