<?php
require_once __DIR__ . '/includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$courts = getCourts();
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Enhanced Booking Page Styles */
.booking-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.booking-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.step-indicator {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
}

.step-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2em;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 3px solid #e9ecef;
    position: relative;
    z-index: 2;
}

.step-item.active .step-circle {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-color: #667eea;
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.step-item.completed .step-circle {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-color: #28a745;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.step-line {
    position: absolute;
    top: 30px;
    left: 50%;
    right: -50%;
    height: 3px;
    background: #e9ecef;
    z-index: 1;
    transition: all 0.4s ease;
}

.step-item.completed .step-line {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.step-title {
    margin-top: 1rem;
    font-weight: 600;
    color: #495057;
    text-align: center;
    transition: color 0.3s ease;
}

.step-item.active .step-title {
    color: #667eea;
}

.step-item.completed .step-title {
    color: #28a745;
}

.booking-content {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    min-height: 500px;
}

.court-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    cursor: pointer;
    position: relative;
    height: 100%;
}

.court-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.court-card.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 20px 40px rgba(40, 167, 69, 0.2);
}

.court-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.court-card:hover .court-image {
    transform: scale(1.05);
}

.court-info {
    padding: 1.5rem;
}

.court-price {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    display: inline-block;
    margin-top: 0.5rem;
}

.time-slot {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.time-slot::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.time-slot:hover::before {
    left: 100%;
}

.time-slot.available {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
}

.time-slot.available:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 25px rgba(40, 167, 69, 0.2);
    border-color: #20c997;
}

.time-slot.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #e7f3ff 0%, #cce7ff 100%);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.time-slot.booked {
    border-color: #dc3545;
    background: linear-gradient(135deg, #fff8f8 0%, #f8e8e8 100%);
    opacity: 0.7;
    cursor: not-allowed;
}

.payment-option {
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.payment-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.payment-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.payment-option:hover .payment-card::before {
    left: 100%;
}

.payment-option:hover .payment-card {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border-color: #667eea;
}

.payment-option.selected .payment-card {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
    transform: scale(1.02);
    box-shadow: 0 15px 35px rgba(40, 167, 69, 0.2);
}

.payment-option.selected .payment-card::after {
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

.payment-methods.has-selection .payment-option:not(.selected) {
    opacity: 0.6;
    transform: scale(0.98);
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
}

.btn-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-enhanced:active {
    transform: translateY(0);
}

.btn-success-enhanced {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-success-enhanced:hover {
    box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
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

.slide-in-right {
    animation: slideInRight 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.booking-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid rgba(0,0,0,0.1);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.summary-item:last-child {
    border-bottom: none;
    font-weight: 600;
    font-size: 1.1em;
    color: #28a745;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid #e9ecef;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="booking-container">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-4">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <i class="fas fa-calendar-check me-3"></i>
                    Đặt sân trực tuyến
                </h1>
                <p class="lead text-white-50">Trải nghiệm đặt sân mượt mà và chuyên nghiệp</p>
            </div>
        </div>

        <!-- Main Booking Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="booking-card">
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="step-item active" id="stepIndicator1">
                                <div class="step-circle">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="step-title">Chọn sân</div>
                                <div class="step-line"></div>
                            </div>
                            <div class="step-item" id="stepIndicator2">
                                <div class="step-circle">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="step-title">Chọn thời gian</div>
                                <div class="step-line"></div>
                            </div>
                            <div class="step-item" id="stepIndicator3">
                                <div class="step-circle">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="step-title">Thanh toán</div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Content -->
                    <div class="booking-content">
                        <!-- Step 1: Chọn sân -->
                        <div class="booking-step fade-in" id="step1">
                            <h4 class="fw-bold mb-4 text-center">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                Chọn sân cầu lông
                            </h4>
                            
                            <div class="row g-4" id="courtsContainer">
                                <?php foreach ($courts as $court): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="court-card" data-court-id="<?php echo $court['id']; ?>" 
                                         data-court-name="<?php echo escape($court['name']); ?>"
                                         data-court-price="<?php echo $court['price_per_hour']; ?>">
                                        <img src="<?php echo escape($court['cover_image']); ?>" 
                                             class="court-image" alt="<?php echo escape($court['name']); ?>">
                                        <div class="court-info">
                                            <h5 class="fw-bold mb-2"><?php echo escape($court['name']); ?></h5>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo escape($court['location']); ?>
                                            </p>
                                            <div class="court-price">
                                                <i class="fas fa-money-bill-wave me-1"></i>
                                                <?php echo number_format($court['price_per_hour']); ?>đ/giờ
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="text-center mt-4">
                                <button class="btn btn-enhanced btn-lg" id="nextToStep2" disabled>
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Tiếp tục chọn thời gian
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Chọn thời gian -->
                        <div class="booking-step d-none" id="step2">
                            <h4 class="fw-bold mb-4 text-center">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Chọn thời gian
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Chọn ngày</label>
                                        <input type="date" class="form-control form-control-lg" 
                                               id="bookingDate" min="<?php echo date('Y-m-d'); ?>" 
                                               value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Thời lượng</label>
                                        <select class="form-select form-select-lg" id="duration">
                                            <option value="1">1 giờ</option>
                                            <option value="2">2 giờ</option>
                                            <option value="3">3 giờ</option>
                                            <option value="4">4 giờ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Khung giờ có sẵn</label>
                                <div class="row g-2" id="timeSlotsContainer">
                                    <!-- Time slots will be loaded here -->
                                </div>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-outline-secondary me-3" id="backToStep1">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </button>
                                <button class="btn btn-enhanced btn-lg" id="nextToStep3" disabled>
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Tiếp tục thanh toán
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Thanh toán -->
                        <div class="booking-step d-none" id="step3">
                            <h4 class="fw-bold mb-4 text-center">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                Xác nhận và thanh toán
                            </h4>

                            <div class="row">
                                <div class="col-md-7">
                                    <!-- Payment Methods -->
                                    <div class="payment-methods mb-4">
                                        <h6 class="fw-bold mb-3">Phương thức thanh toán</h6>
                                        
                                        <div class="payment-option selected" data-method="cash">
                                            <div class="payment-card">
                                                <div class="d-flex align-items-center">
                                                    <input type="radio" name="paymentMethod" value="cash" 
                                                           class="form-check-input me-3" checked>
                                                    <div class="payment-icon me-3">
                                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 d-inline-flex">
                                                            <i class="fas fa-money-bill-wave text-success fa-lg"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Tiền mặt</div>
                                                        <small class="text-muted">Thanh toán tại sân khi đến chơi</small>
                                                    </div>
                                                    <span class="badge bg-success bg-opacity-20 text-success">
                                                        <i class="fas fa-star me-1"></i>Phổ biến
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payment-option" data-method="momo">
                                            <div class="payment-card">
                                                <div class="d-flex align-items-center">
                                                    <input type="radio" name="paymentMethod" value="momo" 
                                                           class="form-check-input me-3">
                                                    <div class="payment-icon me-3">
                                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 d-inline-flex">
                                                            <i class="fab fa-cc-mastercard text-danger fa-lg"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Ví MoMo</div>
                                                        <small class="text-muted">Thanh toán qua ví điện tử</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payment-option" data-method="vnpay">
                                            <div class="payment-card">
                                                <div class="d-flex align-items-center">
                                                    <input type="radio" name="paymentMethod" value="vnpay" 
                                                           class="form-check-input me-3">
                                                    <div class="payment-icon me-3">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-inline-flex">
                                                            <i class="fas fa-university text-primary fa-lg"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">VNPay</div>
                                                        <small class="text-muted">Thanh toán qua ngân hàng</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Ghi chú (tùy chọn)</label>
                                        <textarea class="form-control" id="bookingNotes" rows="3" 
                                                  placeholder="Nhập ghi chú cho booking của bạn..."></textarea>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <!-- Booking Summary -->
                                    <div class="booking-summary">
                                        <h6 class="fw-bold mb-3">
                                            <i class="fas fa-receipt me-2"></i>
                                            Thông tin đặt sân
                                        </h6>
                                        
                                        <div class="summary-item">
                                            <span>Sân:</span>
                                            <span id="summaryCourtName">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span>Ngày:</span>
                                            <span id="summaryDate">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span>Giờ:</span>
                                            <span id="summaryTime">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span>Thời lượng:</span>
                                            <span id="summaryDuration">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span>Tổng tiền:</span>
                                            <span id="summaryTotal" class="fw-bold text-success">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button class="btn btn-outline-secondary me-3" id="backToStep2">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </button>
                                <button class="btn btn-success-enhanced btn-lg" id="confirmBooking">
                                    <i class="fas fa-check me-2"></i>
                                    Xác nhận đặt sân
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                        <i class="fas fa-check-circle text-success fa-3x"></i>
                    </div>
                    <h4 class="fw-bold text-success">Đặt sân thành công!</h4>
                    <p class="text-muted">Booking của bạn đã được xác nhận</p>
                </div>
                
                <div class="booking-details bg-light rounded-3 p-3 mb-4">
                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">Mã booking:</small>
                            <div class="fw-bold" id="modalBookingCode">-</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Sân:</small>
                            <div class="fw-bold" id="modalCourtName">-</div>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted">Thời gian:</small>
                            <div class="fw-bold" id="modalDateTime">-</div>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted">Tổng tiền:</small>
                            <div class="fw-bold text-success" id="modalTotal">-</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="booking-history.php" class="btn btn-success btn-lg">
                        <i class="fas fa-history me-2"></i>Xem lịch sử đặt sân
                    </a>
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay d-none" id="loadingOverlay">
    <div class="text-center">
        <div class="loading-spinner mb-3"></div>
        <h5 class="text-primary">Đang xử lý...</h5>
        <p class="text-muted">Vui lòng chờ trong giây lát</p>
    </div>
</div>

<script src="<?php echo asset('assets/js/booking-online-new.js'); ?>"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>