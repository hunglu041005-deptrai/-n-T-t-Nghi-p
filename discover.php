<?php
require_once __DIR__ . '/includes/functions.php';

// Chặn admin truy cập trang web thường
blockAdminFromPublic();

// Get filter parameters
$filters = [
    'service' => $_GET['service'] ?? '',
    'area' => $_GET['area'] ?? '',
    'type' => $_GET['type'] ?? ''
];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Discover Page Header -->
<section class="bg-info text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">🔍 Khám phá dịch vụ</h1>
                <p class="mb-0 opacity-75">Tìm hiểu thêm về các dịch vụ và tiện ích của chúng tôi</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="index.php" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Về trang chủ
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Service Filter Bar -->
<section class="bg-light py-3 border-bottom">
    <div class="container">
        <form class="row g-2 align-items-end" method="get" action="discover.php">
            <div class="col-sm-6 col-md-4">
                <label class="form-label fw-bold">Loại dịch vụ</label>
                <select name="service" class="form-select">
                    <option value="">Tất cả dịch vụ</option>
                    <option value="booking" <?php echo $filters['service'] === 'booking' ? 'selected' : ''; ?>>Đặt sân</option>
                    <option value="membership" <?php echo $filters['service'] === 'membership' ? 'selected' : ''; ?>>Gói hội viên</option>
                    <option value="training" <?php echo $filters['service'] === 'training' ? 'selected' : ''; ?>>Đào tạo</option>
                    <option value="equipment" <?php echo $filters['service'] === 'equipment' ? 'selected' : ''; ?>>Thiết bị</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label fw-bold">Khu vực</label>
                <select name="area" class="form-select">
                    <option value="">Tất cả khu vực</option>
                    <option value="center" <?php echo $filters['area'] === 'center' ? 'selected' : ''; ?>>Trung tâm</option>
                    <option value="north" <?php echo $filters['area'] === 'north' ? 'selected' : ''; ?>>Phía Bắc</option>
                    <option value="south" <?php echo $filters['area'] === 'south' ? 'selected' : ''; ?>>Phía Nam</option>
                    <option value="east" <?php echo $filters['area'] === 'east' ? 'selected' : ''; ?>>Phía Đông</option>
                    <option value="west" <?php echo $filters['area'] === 'west' ? 'selected' : ''; ?>>Phía Tây</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label fw-bold">Đối tượng</label>
                <select name="type" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="individual" <?php echo $filters['type'] === 'individual' ? 'selected' : ''; ?>>Cá nhân</option>
                    <option value="group" <?php echo $filters['type'] === 'group' ? 'selected' : ''; ?>>Nhóm</option>
                    <option value="corporate" <?php echo $filters['type'] === 'corporate' ? 'selected' : ''; ?>>Doanh nghiệp</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label opacity-0">Tìm</label>
                <button type="submit" class="btn btn-info w-100">
                    <i class="fas fa-search me-2"></i>Tìm kiếm
                </button>
            </div>
            <div class="col-sm-6 col-md-1">
                <label class="form-label opacity-0">Reset</label>
                <a href="discover.php" class="btn btn-outline-secondary w-100" title="Xóa bộ lọc">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</section>

<!-- Main Discover Section -->
<section class="discover-page-section">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="discover-content p-4">
                    <!-- Service Categories -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <h3 class="fw-bold mb-4">🎯 Dịch vụ chính</h3>
                        </div>
                        
                        <!-- Booking Service -->
                        <div class="col-md-6">
                            <div class="service-card h-100 p-4 bg-white rounded-4 shadow-sm border">
                                <div class="service-header d-flex align-items-center mb-3">
                                    <div class="service-icon bg-primary text-white rounded-circle me-3">
                                        <i class="fas fa-calendar-check fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Đặt sân trực tuyến</h5>
                                        <small class="text-muted">Nhanh chóng & Tiện lợi</small>
                                    </div>
                                </div>
                                
                                <p class="text-muted mb-3">Hệ thống đặt sân hiện đại với giao diện thân thiện, thanh toán đa dạng và quản lý lịch thông minh.</p>
                                
                                <div class="features-list mb-4">
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Đặt sân 24/7 online</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Thanh toán MoMo, VNPay</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Xác nhận tức thì</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Hủy miễn phí trước 2h</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="booking-online.php" class="btn btn-primary flex-fill">
                                        <i class="fas fa-play me-2"></i>Đặt ngay
                                    </a>
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bookingGuideModal">
                                        <i class="fas fa-question-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Membership Plans -->
                        <div class="col-md-6">
                            <div class="service-card p-4 bg-white rounded-4 shadow-sm border">
                                <div class="service-header d-flex align-items-center mb-3">
                                    <div class="service-icon bg-success text-white rounded-circle me-3">
                                        <i class="fas fa-id-card fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Gói hội viên</h5>
                                        <small class="text-muted">Ưu đãi & Tiết kiệm</small>
                                    </div>
                                </div>

                                <p class="text-muted mb-3">Chơi thả ga với gói hội viên ưu đãi — mua combo vé, tặng vé miễn phí, thời hạn linh hoạt.</p>

                                <div class="features-list mb-4">
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Combo vé ưu đãi, tặng vé miễn phí</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Thời hạn linh hoạt 3 – 12 tháng</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Nhiều khung giờ: sáng, chiều, tối</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Tiết kiệm đến 10% so với giá lẻ</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="membership.php" class="btn btn-success flex-fill" style="background: linear-gradient(135deg,#28a745,#20c997); border:none; border-radius:12px; font-weight:600;">
                                        <i class="fas fa-play me-2"></i>Xem gói hội viên
                                    </a>
                                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#membershipModal" style="border-radius:12px;">
                                        <i class="fas fa-question-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Training Service -->
                        <div class="col-md-6">
                            <div class="service-card h-100 p-4 bg-white rounded-4 shadow-sm border">
                                <div class="service-header d-flex align-items-center mb-3">
                                    <div class="service-icon bg-warning text-dark rounded-circle me-3">
                                        <i class="fas fa-graduation-cap fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Đào tạo cầu lông</h5>
                                        <small class="text-muted">Từ cơ bản đến nâng cao</small>
                                    </div>
                                </div>
                                
                                <p class="text-muted mb-3">Khóa học cầu lông với huấn luyện viên giàu kinh nghiệm, phương pháp hiện đại và lộ trình rõ ràng.</p>
                                
                                <div class="features-list mb-4">
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>HLV chuyên nghiệp</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Lớp nhỏ 4-6 người</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Thiết bị đầy đủ</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Chứng chỉ hoàn thành</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="training.php" class="btn btn-warning flex-fill">
                                        <i class="fas fa-user-plus me-2"></i>Đăng ký
                                    </a>
                                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#trainingModal">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Equipment Service -->
                        <div class="col-md-6">
                            <div class="service-card h-100 p-4 bg-white rounded-4 shadow-sm border">
                                <div class="service-header d-flex align-items-center mb-3">
                                    <div class="service-icon bg-danger text-white rounded-circle me-3">
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Thiết bị & Phụ kiện</h5>
                                        <small class="text-muted">Chính hãng & Uy tín</small>
                                    </div>
                                </div>
                                
                                <p class="text-muted mb-3">Cửa hàng thiết bị cầu lông với đầy đủ vợt, giày, quần áo và phụ kiện từ các thương hiệu nổi tiếng.</p>
                                
                                <div class="features-list mb-4">
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Vợt Yonex, Victor, Lining</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Giày chuyên dụng</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Quần áo thể thao</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>Bảo hành chính hãng</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="equipment.php" class="btn btn-danger flex-fill">
                                        <i class="fas fa-store me-2"></i>Xem shop
                                    </a>
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#equipmentModal">
                                        <i class="fas fa-tags"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Services -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <h3 class="fw-bold mb-4">🌟 Dịch vụ bổ sung</h3>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="additional-service-card text-center p-4 bg-light rounded-4">
                                <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                                <h6 class="fw-bold">Ứng dụng di động</h6>
                                <p class="text-muted small">Tải app để trải nghiệm tốt hơn</p>
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="#" class="btn btn-outline-dark btn-sm">iOS</a>
                                    <a href="#" class="btn btn-outline-dark btn-sm">Android</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="additional-service-card text-center p-4 bg-light rounded-4">
                                <i class="fas fa-headset fa-3x text-success mb-3"></i>
                                <h6 class="fw-bold">Hỗ trợ 24/7</h6>
                                <p class="text-muted small">Tư vấn và giải đáp mọi thắc mắc</p>
                                <button class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-phone me-1"></i>Hotline
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="additional-service-card text-center p-4 bg-light rounded-4">
                                <i class="fas fa-gift fa-3x text-warning mb-3"></i>
                                <h6 class="fw-bold">Chương trình VIP</h6>
                                <p class="text-muted small">Ưu đãi đặc biệt cho thành viên</p>
                                <button class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-crown me-1"></i>Tham gia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="discover-sidebar bg-light h-100 p-4">
                    <!-- Quick Links -->
                    <div class="sidebar-section mb-4">
                        <h6 class="fw-bold mb-3">🚀 Liên kết nhanh</h6>
                        <div class="d-grid gap-2">
                            <a href="booking-online.php" class="btn btn-outline-info btn-sm text-start">
                                <i class="fas fa-search me-2"></i>Tìm sân ngay
                            </a>
                            <a href="map.php" class="btn btn-outline-info btn-sm text-start">
                                <i class="fas fa-map me-2"></i>Xem bản đồ
                            </a>
                            <a href="featured.php" class="btn btn-outline-info btn-sm text-start">
                                <i class="fas fa-star me-2"></i>Sân nổi bật
                            </a>
                            <a href="<?php echo isLoggedIn() ? 'profile.php' : 'register.php'; ?>" class="btn btn-outline-info btn-sm text-start">
                                <i class="fas fa-user me-2"></i>Tài khoản
                            </a>
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="sidebar-section mb-4">
                        <h6 class="fw-bold mb-3">📞 Liên hệ</h6>
                        <div class="contact-info">
                            <div class="contact-item mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <span>1900 1234</span>
                            </div>
                            <div class="contact-item mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <span>info@badmintonpro.vn</span>
                            </div>
                            <div class="contact-item mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span>Hà Nội, Việt Nam</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <span>6:00 - 22:00</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ -->
                    <div class="sidebar-section">
                        <h6 class="fw-bold mb-3">❓ Câu hỏi thường gặp</h6>
                        <div class="accordion accordion-flush" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Làm sao để đặt sân?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body small">
                                        Bạn có thể đặt sân trực tuyến qua website hoặc app, chọn sân và khung giờ phù hợp.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Có thể hủy đặt sân không?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body small">
                                        Có thể hủy miễn phí trước 2 giờ. Sau thời gian này sẽ tính phí hủy.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Có cho thuê vợt không?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body small">
                                        Có, chúng tôi có dịch vụ cho thuê vợt và cầu với giá ưu đãi.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Modals -->
<!-- Booking Guide Modal -->
<div class="modal fade" id="bookingGuideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hướng dẫn đặt sân</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="step mb-3">
                    <h6><span class="badge bg-primary me-2">1</span>Chọn sân</h6>
                    <p class="small text-muted">Tìm kiếm và chọn sân phù hợp với vị trí và giá cả.</p>
                </div>
                <div class="step mb-3">
                    <h6><span class="badge bg-primary me-2">2</span>Chọn giờ</h6>
                    <p class="small text-muted">Chọn khung giờ trống phù hợp với lịch trình.</p>
                </div>
                <div class="step mb-3">
                    <h6><span class="badge bg-primary me-2">3</span>Thanh toán</h6>
                    <p class="small text-muted">Thanh toán online hoặc trả tiền mặt tại sân.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Discover page specific scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Service card hover effects
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
    
    // Additional service card animations
    document.querySelectorAll('.additional-service-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            icon.style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            icon.style.transform = 'scale(1) rotate(0deg)';
        });
    });
});
</script>