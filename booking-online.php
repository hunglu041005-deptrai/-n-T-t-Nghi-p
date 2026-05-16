<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/booking-system.php';

$courts = getCourts();
$bookingSystem = new AdvancedBookingSystem();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Booking Online Page Header -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">
                    <i class="fas fa-calendar-check me-2"></i>Đặt sân trực tuyến
                </h1>
                <p class="mb-0 opacity-75">Nhanh chóng & Tiện lợi - Đặt sân 24/7 online</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="discover.php" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Booking Steps Guide -->
<section class="bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="booking-steps d-flex justify-content-center">
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Chọn sân</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Chọn giờ</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step-item" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Thanh toán</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Booking Section -->
<section class="booking-online-section py-5">
    <div class="container">
        <div class="row">
            <!-- Court Selection -->
            <div class="col-lg-8">
                <div class="booking-step" id="step1">
                    <h4 class="fw-bold mb-4">
                        <i class="fas fa-badminton-shuttlecock text-primary me-2"></i>
                        Bước 1: Chọn sân cầu lông
                    </h4>
                    
                    <!-- Quick Filters -->
                    <div class="quick-filters mb-4">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select class="form-select" id="locationFilter">
                                    <option value="">Tất cả khu vực</option>
                                    <option value="Hoàng Mai">Hoàng Mai</option>
                                    <option value="Thanh Xuân">Thanh Xuân</option>
                                    <option value="Cầu Giấy">Cầu Giấy</option>
                                    <option value="Đống Đa">Đống Đa</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="priceFilter">
                                    <option value="">Tất cả giá</option>
                                    <option value="low">Dưới 100k</option>
                                    <option value="mid">100k - 150k</option>
                                    <option value="high">Trên 150k</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateFilter" 
                                       value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100" id="applyFilters">
                                    <i class="fas fa-filter me-2"></i>Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Courts Grid -->
                    <div class="courts-grid" id="courtsGrid">
                        <?php foreach ($courts as $court): ?>
                            <div class="court-booking-card" data-court-id="<?php echo $court['id']; ?>">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="<?php echo escape($court['cover_image']); ?>" 
                                             class="court-image" alt="<?php echo escape($court['name']); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo escape($court['name']); ?></h5>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                <?php echo escape($court['location']); ?>
                                            </p>
                                            <p class="text-success fw-bold mb-2">
                                                <i class="fas fa-money-bill me-2"></i>
                                                <?php echo number_format($court['price_per_hour']); ?>đ/giờ
                                            </p>
                                            
                                            <div class="court-features mb-3">
                                                <span class="badge bg-light text-dark me-1">Có mái che</span>
                                                <span class="badge bg-light text-dark me-1">Sân gỗ</span>
                                                <span class="badge bg-light text-dark">Điều hòa</span>
                                            </div>
                                            
                                            <button class="btn btn-primary select-court-btn" 
                                                    data-court-id="<?php echo $court['id']; ?>"
                                                    data-court-name="<?php echo escape($court['name']); ?>"
                                                    data-court-price="<?php echo $court['price_per_hour']; ?>">
                                                <i class="fas fa-check me-2"></i>Chọn sân này
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Time Selection -->
                <div class="booking-step d-none" id="step2">
                    <h4 class="fw-bold mb-4">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Bước 2: Chọn thời gian
                    </h4>
                    
                    <div class="selected-court-info mb-4 p-3 bg-light rounded">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1" id="selectedCourtName">Sân đã chọn</h6>
                                <p class="text-muted mb-0" id="selectedCourtPrice">Giá: 0đ/giờ</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button class="btn btn-outline-secondary btn-sm" id="changeCourtBtn">
                                    <i class="fas fa-edit me-2"></i>Đổi sân
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Selection -->
                    <div class="date-selection mb-4">
                        <label class="form-label fw-bold">Chọn ngày:</label>
                        <div class="date-picker-container">
                            <input type="date" class="form-control" id="bookingDate" 
                                   value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <!-- Time Slots -->
                    <div class="time-slots-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">Chọn khung giờ:</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadTimeSlots()">
                                <i class="fas fa-refresh me-1"></i>Tải lại
                            </button>
                        </div>
                        <div class="time-slots-grid" id="timeSlotsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                            <!-- Time slots will be loaded dynamically -->
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="mt-2 text-muted">Đang tải khung giờ...</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-actions mt-4">
                        <button class="btn btn-outline-secondary me-2" id="backToStep1">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </button>
                        <button class="btn btn-primary" id="proceedToPayment" disabled>
                            <i class="fas fa-arrow-right me-2"></i>Tiếp tục thanh toán
                        </button>
                    </div>
                </div>
                
                <!-- Payment -->
                <div class="booking-step d-none" id="step3">
                    <h4 class="fw-bold mb-4">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Bước 3: Thanh toán
                    </h4>
                    
                    <!-- Booking Summary -->
                    <div class="booking-summary mb-4 p-4 bg-light rounded">
                        <h6 class="fw-bold mb-3">Thông tin đặt sân</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Sân:</strong> <span id="summaryCourtName">-</span></p>
                                <p><strong>Ngày:</strong> <span id="summaryDate">-</span></p>
                                <p><strong>Giờ:</strong> <span id="summaryTime">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Thời lượng:</strong> <span id="summaryDuration">1 giờ</span></p>
                                <p><strong>Giá/giờ:</strong> <span id="summaryPricePerHour">-</span></p>
                                <p class="h5 text-success"><strong>Tổng tiền:</strong> <span id="summaryTotal">-</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods -->
                    <div class="payment-methods mb-4">
                        <h6 class="fw-bold mb-3">Phương thức thanh toán</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="payment-option" data-method="momo">
                                    <div class="payment-card p-3 border rounded">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="paymentMethod" value="momo" class="form-check-input me-3">
                                            <img src="https://via.placeholder.com/40x40?text=MoMo" class="me-3" alt="MoMo">
                                            <div>
                                                <div class="fw-bold">Ví MoMo</div>
                                                <small class="text-muted">Thanh toán qua ví điện tử</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-option" data-method="vnpay">
                                    <div class="payment-card p-3 border rounded">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="paymentMethod" value="vnpay" class="form-check-input me-3">
                                            <img src="https://via.placeholder.com/40x40?text=VNPay" class="me-3" alt="VNPay">
                                            <div>
                                                <div class="fw-bold">VNPay</div>
                                                <small class="text-muted">Thanh toán qua ngân hàng</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-option" data-method="cash">
                                    <div class="payment-card p-3 border rounded">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="paymentMethod" value="cash" class="form-check-input me-3" checked>
                                            <i class="fas fa-money-bill-wave fa-2x text-success me-3"></i>
                                            <div>
                                                <div class="fw-bold">Tiền mặt</div>
                                                <small class="text-muted">Thanh toán tại sân</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Notes -->
                    <div class="additional-notes mb-4">
                        <label class="form-label fw-bold">Ghi chú (tùy chọn)</label>
                        <textarea class="form-control" id="bookingNotes" rows="3" 
                                  placeholder="Nhập ghi chú cho booking của bạn..."></textarea>
                    </div>
                    
                    <div class="booking-actions">
                        <button class="btn btn-outline-secondary me-2" id="backToStep2">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </button>
                        <button class="btn btn-success btn-lg" id="confirmBooking">
                            <i class="fas fa-check me-2"></i>Xác nhận đặt sân
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="booking-sidebar">
                    <!-- Features -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-star me-2"></i>Tính năng nổi bật
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="feature-item mb-3">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Đặt sân 24/7 online</strong>
                                <p class="small text-muted mb-0">Đặt sân bất cứ lúc nào, ở bất cứ đâu</p>
                            </div>
                            <div class="feature-item mb-3">
                                <i class="fas fa-credit-card text-success me-2"></i>
                                <strong>Thanh toán MoMo, VNPay</strong>
                                <p class="small text-muted mb-0">Đa dạng phương thức thanh toán</p>
                            </div>
                            <div class="feature-item mb-3">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <strong>Xác nhận tức thì</strong>
                                <p class="small text-muted mb-0">Nhận xác nhận booking ngay lập tức</p>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-times-circle text-warning me-2"></i>
                                <strong>Hủy miễn phí trước 2h</strong>
                                <p class="small text-muted mb-0">Linh hoạt thay đổi kế hoạch</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Help -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-question-circle me-2"></i>Cần hỗ trợ?
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted">Liên hệ với chúng tôi nếu bạn cần hỗ trợ trong quá trình đặt sân.</p>
                            <div class="d-grid gap-2">
                                <a href="tel:1900123456" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-phone me-2"></i>Hotline: 1900 123 456
                                </a>
                                <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#helpModal">
                                    <i class="fas fa-comments me-2"></i>Chat hỗ trợ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Success Modal -->
<div class="modal fade" id="bookingSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="success-icon mb-3">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h4 class="fw-bold text-success mb-3">Đặt sân thành công!</h4>
                <p class="text-muted mb-4">Booking của bạn đã được xác nhận. Chúng tôi sẽ gửi thông tin chi tiết qua email.</p>
                <div class="booking-details bg-light p-3 rounded mb-4">
                    <p class="mb-1"><strong>Mã booking:</strong> <span id="bookingCode">-</span></p>
                    <p class="mb-1"><strong>Sân:</strong> <span id="finalCourtName">-</span></p>
                    <p class="mb-1"><strong>Thời gian:</strong> <span id="finalDateTime">-</span></p>
                    <p class="mb-0"><strong>Tổng tiền:</strong> <span id="finalTotal" class="text-success">-</span></p>
                </div>
                <div class="d-grid gap-2">
                    <a href="booking-history.php" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i>Xem lịch sử đặt sân
                    </a>
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/booking-online.js"></script>