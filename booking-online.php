<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/booking-system.php';

// Bắt buộc đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$courts = getCourts();

// Lấy court_id từ URL nếu có
$preselected_court_id = $_GET['court_id'] ?? null;
$preselected_court = null;
if ($preselected_court_id) {
    $preselected_court = getCourtById($preselected_court_id);
}

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
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-credit-card text-primary me-2"></i>
                            Phương thức thanh toán
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="payment-option" data-method="momo">
                                    <div class="payment-card p-4 border rounded-3">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="paymentMethod" value="momo" class="form-check-input me-3">
                                            <div class="payment-icon me-3">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-2 d-inline-flex">
                                                    <i class="fab fa-cc-mastercard text-danger fa-lg"></i>
                                                </div>
                                            </div>
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
                                    <div class="payment-card p-4 border rounded-3">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="paymentMethod" value="vnpay" class="form-check-input me-3">
                                            <div class="payment-icon me-3">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-inline-flex">
                                                    <i class="fas fa-university text-primary fa-lg"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">VNPay</div>
                                                <small class="text-muted">Thanh toán qua ngân hàng</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-option" data-method="cash">
                                    <div class="payment-card p-4 border rounded-3">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="paymentMethod" value="cash" class="form-check-input me-3" checked>
                                            <div class="payment-icon me-3">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2 d-inline-flex">
                                                    <i class="fas fa-money-bill-wave text-success fa-lg"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Tiền mặt</div>
                                                <small class="text-muted">Thanh toán tại sân khi đến chơi</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success bg-opacity-20 text-success">
                                                    <i class="fas fa-star me-1"></i>Phổ biến
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Info -->
                        <div class="payment-info mt-3 p-3 bg-light rounded-3">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt text-success me-1"></i>
                                        <strong>Bảo mật:</strong> Tất cả giao dịch được mã hóa và bảo mật
                                    </small>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Xử lý tức thì
                                    </small>
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
                    <div style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);border:1px solid #f0f0f0;">
                        <!-- Header -->
                        <div style="background:linear-gradient(135deg,#28a745,#20c997);padding:1.1rem 1.3rem;display:flex;align-items:center;gap:.7rem;">
                            <div style="width:34px;height:34px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="17" height="17" viewBox="0 0 32 32" fill="none"><path d="M6 20v-4a10 10 0 0120 0v4" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/><rect x="4" y="18" width="5" height="8" rx="2.5" fill="#fff"/><rect x="23" y="18" width="5" height="8" rx="2.5" fill="#fff"/></svg>
                            </div>
                            <div>
                                <div style="font-weight:800;color:#fff;font-size:.93rem;">Hỗ trợ 24/7</div>
                                <div style="color:rgba(255,255,255,.8);font-size:.72rem;display:flex;align-items:center;gap:.3rem;">
                                    <span style="width:7px;height:7px;background:#a3e635;border-radius:50%;display:inline-block;"></span>
                                    Đang trực tuyến
                                </div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div style="padding:1.2rem;">
                            <p style="font-size:.82rem;color:#6b7280;margin-bottom:1rem;line-height:1.55;">
                                Tư vấn và giải đáp mọi thắc mắc về đặt sân qua nhiều kênh.
                            </p>

                            <!-- Gọi điện -->
                            <a href="tel:0968073500"
                               style="display:flex;align-items:center;justify-content:center;gap:.6rem;
                                      background:linear-gradient(135deg,#28a745,#20c997);
                                      border-radius:12px;padding:.75rem;
                                      font-weight:700;font-size:.88rem;color:#fff;text-decoration:none;
                                      box-shadow:0 4px 14px rgba(40,167,69,.3);transition:all .2s;
                                      margin-bottom:.6rem;"
                               onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(40,167,69,.4)'"
                               onmouseout="this.style.transform='';this.style.boxShadow='0 4px 14px rgba(40,167,69,.3)'">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 2h3l1.5 3.5-1.75 1.05a9 9 0 004.7 4.7L11.5 9.5 15 11v3a2 2 0 01-2 2C5.37 16 0 10.63 0 4a2 2 0 012-2h1z" fill="#fff" opacity=".9"/></svg>
                                Gọi: 0968.073.500
                            </a>

                            <!-- Chat -->
                            <button onclick="openBookingChat()"
                                    style="display:flex;align-items:center;justify-content:center;gap:.6rem;
                                           border:1.5px solid #28a745;border-radius:12px;padding:.7rem;
                                           font-weight:700;font-size:.88rem;color:#28a745;background:#fff;
                                           width:100%;cursor:pointer;transition:all .2s;margin-bottom:.6rem;"
                                    onmouseover="this.style.background='#f0fdf4'"
                                    onmouseout="this.style.background='#fff'">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"><path d="M1 3a2 2 0 012-2h11a2 2 0 012 2v7a2 2 0 01-2 2H6l-4 4V3z" fill="#28a745" opacity=".12" stroke="#28a745" stroke-width="1.4" stroke-linejoin="round"/><circle cx="5.5" cy="6.5" r="1" fill="#28a745"/><circle cx="8.5" cy="6.5" r="1" fill="#28a745"/><circle cx="11.5" cy="6.5" r="1" fill="#28a745"/></svg>
                                Chat trực tuyến
                            </button>

                            <!-- Email -->
                            <a href="mailto:support@badmintonpro.vn"
                               style="display:flex;align-items:center;justify-content:center;gap:.6rem;
                                      border:1.5px solid #e5e7eb;border-radius:12px;padding:.7rem;
                                      font-weight:600;font-size:.88rem;color:#4b5563;text-decoration:none;
                                      background:#fff;transition:all .2s;margin-bottom:.9rem;"
                               onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db'"
                               onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb'">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="3" width="14" height="10" rx="2" stroke="#6b7280" stroke-width="1.3"/><path d="M1 5l7 5 7-5" stroke="#6b7280" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Gửi email
                            </a>

                            <!-- Phản hồi note -->
                            <div style="display:flex;align-items:center;justify-content:center;gap:.4rem;font-size:.76rem;color:#6b7280;">
                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="6.5" cy="6.5" r="5.5" fill="#28a745" opacity=".1" stroke="#28a745" stroke-width="1.1"/><path d="M6.5 4v3l1.5 1" stroke="#28a745" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Phản hồi trong vòng 5 phút
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

<!-- Payment Confirmation Modal (MoMo / Bank Transfer) -->
<div class="modal fade" id="paymentConfirmModal" tabindex="-1" aria-labelledby="paymentConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,.2);">
            <div class="modal-header" id="paymentConfirmModalHeader" style="padding:1.2rem 1.5rem;border-bottom:1px solid #f3f4f6;">
                <h5 class="modal-title fw-bold" id="paymentConfirmModalLabel">
                    <i id="pmModalIcon" class="fas fa-wallet me-2"></i>
                    <span id="pmModalTitle">Xác nhận thanh toán</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:1.5rem;">

                <!-- MoMo Panel -->
                <div id="pmMomoPanel">
                    <div style="background:#fdf2f8;border:1px solid #f9a8d4;border-radius:14px;padding:1rem 1.2rem;margin-bottom:1rem;">
                        <div style="font-weight:700;font-size:.82rem;color:#be185d;margin-bottom:.7rem;">
                            <i class="fas fa-mobile-alt me-1"></i> Chuyển khoản qua MoMo
                        </div>
                        <div style="display:flex;gap:1rem;align-items:flex-start;">
                            <img id="pmMomoQr"
                                 src="https://img.vietqr.io/image/MOMO-0968073500-qr_only.png?amount=0&addInfo=DATSAN&accountName=LU+DANG+HUNG"
                                 alt="QR MoMo"
                                 style="width:120px;height:120px;border-radius:10px;border:2px solid #f9a8d4;padding:3px;background:#fff;flex-shrink:0;">
                            <div style="display:grid;gap:.5rem;font-size:.88rem;">
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Số điện thoại</span>
                                    <strong style="font-family:monospace;color:#db2777;font-size:.95rem;">0968073500</strong>
                                </div>
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Tên tài khoản</span>
                                    <strong>LU DANG HUNG</strong>
                                </div>
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Số tiền</span>
                                    <strong id="pmMomoAmount" style="color:#db2777;">—</strong>
                                </div>
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Nội dung CK</span>
                                    <strong id="pmMomoRef" style="font-family:monospace;color:#db2777;">—</strong>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top:.7rem;background:#fce7f3;border-radius:8px;padding:.5rem .8rem;font-size:.77rem;color:#9d174d;">
                            <i class="fas fa-info-circle me-1"></i>
                            Mở app MoMo → Quét QR hoặc Chuyển tiền → Nhập SĐT → Điền đúng nội dung CK
                        </div>
                    </div>
                </div>

                <!-- Bank / VNPay Panel -->
                <div id="pmBankPanel" style="display:none;">
                    <div style="background:#fffdf0;border:1px solid #fde68a;border-radius:14px;padding:1rem 1.2rem;margin-bottom:1rem;">
                        <div style="font-weight:700;font-size:.82rem;color:#92400e;margin-bottom:.7rem;">
                            <i class="fas fa-university me-1"></i> Chuyển khoản ngân hàng (MB Bank)
                        </div>
                        <div style="display:flex;gap:1rem;align-items:flex-start;">
                            <img id="pmBankQr"
                                 src="https://img.vietqr.io/image/MB-0968073500-qr_only.png?amount=0&addInfo=DATSAN&accountName=LU+DANG+HUNG"
                                 alt="QR MB Bank"
                                 style="width:120px;height:120px;border-radius:10px;border:2px solid #fde68a;padding:3px;background:#fff;flex-shrink:0;">
                            <div style="display:grid;gap:.5rem;font-size:.88rem;">
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Ngân hàng</span>
                                    <strong>MB Bank</strong>
                                </div>
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Số tài khoản</span>
                                    <strong style="font-family:monospace;color:#6366f1;font-size:.95rem;">0968073500</strong>
                                </div>
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Chủ tài khoản</span>
                                    <strong>LU DANG HUNG</strong>
                                </div>
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Số tiền</span>
                                    <strong id="pmBankAmount" style="color:#6366f1;">—</strong>
                                </div>
                                <div style="display:flex;gap:.5rem;">
                                    <span style="color:#78716c;min-width:110px;">Nội dung CK</span>
                                    <strong id="pmBankRef" style="font-family:monospace;color:#6366f1;">—</strong>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top:.7rem;background:#fef9c3;border-radius:8px;padding:.5rem .8rem;font-size:.77rem;color:#854d0e;">
                            <i class="fas fa-info-circle me-1"></i>
                            Ghi đúng nội dung để được xác nhận tự động
                        </div>
                    </div>
                </div>

                <!-- Confirmation checkbox -->
                <div style="padding:.85rem 1rem;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:12px;display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                    <input type="checkbox" id="confirmTransferCheck" onchange="document.getElementById('pmConfirmBtn').disabled = !this.checked"
                           style="width:20px;height:20px;accent-color:#28a745;cursor:pointer;flex-shrink:0;">
                    <label for="confirmTransferCheck" style="font-size:.88rem;font-weight:600;color:#374151;cursor:pointer;margin:0;line-height:1.4;">
                        Tôi đã chuyển khoản thành công
                    </label>
                </div>

                <div style="font-size:.78rem;color:#9ca3af;text-align:center;">
                    <i class="fas fa-shield-alt me-1"></i>
                    Đơn đặt sân sẽ được xác nhận sau khi kiểm tra giao dịch
                </div>
            </div>
            <div class="modal-footer" style="padding:1rem 1.5rem;border-top:1px solid #f3f4f6;gap:.7rem;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px;font-weight:600;">
                    Huỷ
                </button>
                <button type="button" id="pmConfirmBtn" disabled
                        onclick="proceedBookingAfterPayment()"
                        style="background:linear-gradient(135deg,#28a745,#20c997);color:#fff;border:none;border-radius:10px;padding:.6rem 1.5rem;font-weight:700;cursor:pointer;transition:opacity .2s;"
                >
                    <i class="fas fa-check me-2"></i>Xác nhận đặt sân
                </button>
            </div>
        </div>
    </div>
</div>
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

<script>
/* Payment confirm modal button styling */
document.addEventListener('DOMContentLoaded', function() {
    const pmBtn = document.getElementById('pmConfirmBtn');
    if (pmBtn) {
        pmBtn.style.opacity = pmBtn.disabled ? '0.5' : '1';
    }
});
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'confirmTransferCheck') {
        const pmBtn = document.getElementById('pmConfirmBtn');
        if (pmBtn) {
            pmBtn.disabled = !e.target.checked;
            pmBtn.style.opacity = e.target.checked ? '1' : '0.5';
        }
    }
});
</script>

<script>
// ── Chat widget cho booking-online ──
function openBookingChat() {
    const existing = document.getElementById('bookingChatWidget');
    if (existing) { existing.style.display = 'block'; return; }

    const w = document.createElement('div');
    w.id = 'bookingChatWidget';
    w.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;width:340px;z-index:9999;background:#fff;border-radius:20px;box-shadow:0 8px 40px rgba(0,0,0,.2);overflow:hidden;';
    w.innerHTML = `
        <div style="background:linear-gradient(135deg,#28a745,#20c997);padding:1rem 1.2rem;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.6rem;">
                <div style="width:36px;height:36px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 32 32" fill="none"><path d="M6 20v-4a10 10 0 0120 0v4" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/><rect x="4" y="18" width="5" height="8" rx="2.5" fill="#fff"/><rect x="23" y="18" width="5" height="8" rx="2.5" fill="#fff"/></svg>
                </div>
                <div>
                    <div style="color:#fff;font-weight:700;font-size:.9rem;">Hỗ trợ BadmintonPro</div>
                    <div style="color:rgba(255,255,255,.85);font-size:.72rem;display:flex;align-items:center;gap:.3rem;">
                        <span style="width:7px;height:7px;background:#a3e635;border-radius:50%;display:inline-block;"></span>
                        Online — Phản hồi ngay
                    </div>
                </div>
            </div>
            <button onclick="document.getElementById('bookingChatWidget').style.display='none'"
                    style="background:rgba(255,255,255,.2);border:none;color:#fff;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>
        <div id="bookingChatMessages" style="height:230px;overflow-y:auto;padding:1rem;background:#f9fafb;display:flex;flex-direction:column;gap:.7rem;">
            <div style="display:flex;gap:.5rem;align-items:flex-end;">
                <div style="width:30px;height:30px;background:linear-gradient(135deg,#28a745,#20c997);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="14" height="14" viewBox="0 0 32 32" fill="none"><path d="M6 20v-4a10 10 0 0120 0v4" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/><rect x="4" y="18" width="5" height="8" rx="2.5" fill="#fff"/><rect x="23" y="18" width="5" height="8" rx="2.5" fill="#fff"/></svg>
                </div>
                <div style="background:#fff;border-radius:14px 14px 14px 0;padding:.65rem .9rem;font-size:.83rem;color:#374151;max-width:230px;box-shadow:0 1px 4px rgba(0,0,0,.07);">
                    Xin chào! 👋 Bạn cần hỗ trợ gì về việc đặt sân?
                </div>
            </div>
        </div>
        <div style="padding:.75rem;border-top:1px solid #e5e7eb;display:flex;gap:.5rem;background:#fff;">
            <input id="bookingChatInput" type="text" placeholder="Nhập tin nhắn..."
                   style="flex:1;border:1.5px solid #e5e7eb;border-radius:10px;padding:.5rem .85rem;font-size:.85rem;outline:none;"
                   onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#e5e7eb'"
                   onkeydown="if(event.key==='Enter')sendBookingChat()">
            <button onclick="sendBookingChat()" style="background:linear-gradient(135deg,#28a745,#20c997);border:none;color:#fff;border-radius:10px;width:38px;height:38px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M1 7.5h13M8.5 2l6 5.5-6 5.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    `;
    document.body.appendChild(w);
    w.querySelector('#bookingChatInput').focus();
}

function sendBookingChat() {
    const input = document.getElementById('bookingChatInput');
    const msg = input.value.trim();
    if (!msg) return;
    const box = document.getElementById('bookingChatMessages');

    const userBubble = document.createElement('div');
    userBubble.style.cssText = 'display:flex;justify-content:flex-end;';
    userBubble.innerHTML = `<div style="background:linear-gradient(135deg,#28a745,#20c997);border-radius:14px 14px 0 14px;padding:.65rem .9rem;font-size:.83rem;color:#fff;max-width:230px;">${msg}</div>`;
    box.appendChild(userBubble);
    input.value = '';
    box.scrollTop = box.scrollHeight;

    setTimeout(() => {
        const replies = [
            'Cảm ơn bạn! Nhân viên sẽ hỗ trợ ngay.',
            'Bạn có thể gọi 0968.073.500 để được hỗ trợ nhanh hơn.',
            'Chúng tôi hoạt động 6:00–22:00 hàng ngày. Để lại SĐT để chúng tôi gọi lại nhé!',
        ];
        const rep = replies[Math.floor(Math.random() * replies.length)];
        const botBubble = document.createElement('div');
        botBubble.style.cssText = 'display:flex;gap:.5rem;align-items:flex-end;';
        botBubble.innerHTML = `
            <div style="width:30px;height:30px;background:linear-gradient(135deg,#28a745,#20c997);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 32 32" fill="none"><path d="M6 20v-4a10 10 0 0120 0v4" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/><rect x="4" y="18" width="5" height="8" rx="2.5" fill="#fff"/><rect x="23" y="18" width="5" height="8" rx="2.5" fill="#fff"/></svg>
            </div>
            <div style="background:#fff;border-radius:14px 14px 14px 0;padding:.65rem .9rem;font-size:.83rem;color:#374151;max-width:230px;box-shadow:0 1px 4px rgba(0,0,0,.07);">${rep}</div>`;
        box.appendChild(botBubble);
        box.scrollTop = box.scrollHeight;
    }, 900);
}
</script>

<script src="assets/js/booking-online.js"></script>
<?php if ($preselected_court): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động chọn sân nếu có court_id trong URL
    const courtId = '<?php echo $preselected_court['id']; ?>';
    const selectBtn = document.querySelector(`[data-court-id="${courtId}"]`);
    if (selectBtn) {
        // Scroll đến sân được chọn
        selectBtn.closest('.court-booking-card').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
        
        // Highlight sân được chọn
        selectBtn.closest('.court-booking-card').style.border = '2px solid #007bff';
        selectBtn.closest('.court-booking-card').style.backgroundColor = '#f8f9ff';
        
        // Tự động click sau 1 giây
        setTimeout(() => {
            selectBtn.click();
        }, 1000);
    }
});
</script>
<?php endif; ?>