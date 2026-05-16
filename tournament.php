<?php
require_once __DIR__ . '/includes/functions.php';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Tournament Page Header -->
<section class="bg-success text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">
                    <i class="fas fa-trophy me-2"></i>Tổ chức giải đấu
                </h1>
                <p class="mb-0 opacity-75">Chuyên nghiệp & Uy tín - Từ quy mô nhỏ đến lớn</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="discover.php" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Tournament Services -->
<section class="tournament-services py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Dịch vụ tổ chức giải đấu</h2>
                <p class="text-muted">Chúng tôi cung cấp dịch vụ tổ chức giải đấu cầu lông chuyên nghiệp</p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Tournament Planning -->
            <div class="col-lg-4">
                <div class="service-card h-100 p-4 bg-white rounded-4 shadow-sm border">
                    <div class="service-icon bg-success text-white rounded-circle mb-3">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Lập kế hoạch giải đấu</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Tư vấn format giải đấu</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Lập lịch thi đấu</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Phân chia bảng đấu</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Quy định thi đấu</li>
                    </ul>
                    <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#planningModal">
                        <i class="fas fa-info-circle me-2"></i>Tìm hiểu thêm
                    </button>
                </div>
            </div>
            
            <!-- Referee Services -->
            <div class="col-lg-4">
                <div class="service-card h-100 p-4 bg-white rounded-4 shadow-sm border">
                    <div class="service-icon bg-warning text-dark rounded-circle mb-3">
                        <i class="fas fa-whistle fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Trọng tài chuyên nghiệp</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Trọng tài có chứng chỉ</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Kinh nghiệm tổ chức</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Thiết bị chuyên dụng</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Báo cáo kết quả</li>
                    </ul>
                    <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#refereeModal">
                        <i class="fas fa-users me-2"></i>Đội ngũ trọng tài
                    </button>
                </div>
            </div>
            
            <!-- Media Services -->
            <div class="col-lg-4">
                <div class="service-card h-100 p-4 bg-white rounded-4 shadow-sm border">
                    <div class="service-icon bg-danger text-white rounded-circle mb-3">
                        <i class="fas fa-video fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Livestream & Quay phim</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Livestream chất lượng HD</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Quay phim highlight</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Chỉnh sửa video</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Chia sẻ social media</li>
                    </ul>
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#mediaModal">
                        <i class="fas fa-play me-2"></i>Xem demo
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tournament Packages -->
<section class="tournament-packages py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Gói dịch vụ giải đấu</h2>
                <p class="text-muted">Chọn gói phù hợp với quy mô giải đấu của bạn</p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Basic Package -->
            <div class="col-lg-4">
                <div class="package-card h-100 bg-white rounded-4 shadow-sm">
                    <div class="package-header bg-primary text-white text-center p-4 rounded-top">
                        <h4 class="fw-bold mb-2">Gói Cơ bản</h4>
                        <div class="package-price">
                            <span class="h2 fw-bold">5,000,000đ</span>
                            <div class="small">Cho 16-32 người</div>
                        </div>
                    </div>
                    <div class="package-body p-4">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Lập kế hoạch giải đấu</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>2 trọng tài chính</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bảng thi đấu</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Giải thưởng cơ bản</li>
                            <li class="mb-2"><i class="fas fa-times text-muted me-2"></i>Livestream</li>
                            <li class="mb-2"><i class="fas fa-times text-muted me-2"></i>Quay phim</li>
                        </ul>
                        <button class="btn btn-primary w-100 mt-3" data-package="basic">
                            <i class="fas fa-shopping-cart me-2"></i>Chọn gói này
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Premium Package -->
            <div class="col-lg-4">
                <div class="package-card h-100 bg-white rounded-4 shadow-sm border-success border-3 position-relative">
                    <div class="popular-badge">
                        <span class="badge bg-success">Phổ biến nhất</span>
                    </div>
                    <div class="package-header bg-success text-white text-center p-4 rounded-top">
                        <h4 class="fw-bold mb-2">Gói Cao cấp</h4>
                        <div class="package-price">
                            <span class="h2 fw-bold">12,000,000đ</span>
                            <div class="small">Cho 32-64 người</div>
                        </div>
                    </div>
                    <div class="package-body p-4">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Lập kế hoạch giải đấu</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>4 trọng tài chuyên nghiệp</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Bảng thi đấu điện tử</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Giải thưởng cao cấp</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Livestream HD</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Quay phim highlight</li>
                        </ul>
                        <button class="btn btn-success w-100 mt-3" data-package="premium">
                            <i class="fas fa-crown me-2"></i>Chọn gói này
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- VIP Package -->
            <div class="col-lg-4">
                <div class="package-card h-100 bg-white rounded-4 shadow-sm">
                    <div class="package-header bg-warning text-dark text-center p-4 rounded-top">
                        <h4 class="fw-bold mb-2">Gói VIP</h4>
                        <div class="package-price">
                            <span class="h2 fw-bold">25,000,000đ</span>
                            <div class="small">Cho 64+ người</div>
                        </div>
                    </div>
                    <div class="package-body p-4">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Tất cả dịch vụ cao cấp</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>6+ trọng tài quốc tế</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Hệ thống điện tử</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Giải thưởng VIP</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Multi-camera livestream</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Dịch vụ catering</li>
                        </ul>
                        <button class="btn btn-warning w-100 mt-3" data-package="vip">
                            <i class="fas fa-star me-2"></i>Chọn gói này
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tournament Registration Form -->
<section class="tournament-registration py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-clipboard-check me-2"></i>Đăng ký tổ chức giải đấu
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="tournamentForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tên giải đấu *</label>
                                    <input type="text" class="form-control" name="tournament_name" required
                                           placeholder="VD: Giải cầu lông mở rộng 2024">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Người tổ chức *</label>
                                    <input type="text" class="form-control" name="organizer_name" required
                                           placeholder="Tên người/tổ chức">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email liên hệ *</label>
                                    <input type="email" class="form-control" name="email" required
                                           placeholder="email@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số điện thoại *</label>
                                    <input type="tel" class="form-control" name="phone" required
                                           placeholder="0123456789">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ngày tổ chức *</label>
                                    <input type="date" class="form-control" name="tournament_date" required
                                           min="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số lượng người tham gia *</label>
                                    <select class="form-select" name="participants" required>
                                        <option value="">Chọn số lượng</option>
                                        <option value="16-32">16-32 người</option>
                                        <option value="32-64">32-64 người</option>
                                        <option value="64+">Trên 64 người</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Gói dịch vụ *</label>
                                    <select class="form-select" name="package" required id="packageSelect">
                                        <option value="">Chọn gói dịch vụ</option>
                                        <option value="basic">Gói Cơ bản - 5,000,000đ</option>
                                        <option value="premium">Gói Cao cấp - 12,000,000đ</option>
                                        <option value="vip">Gói VIP - 25,000,000đ</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Địa điểm mong muốn</label>
                                    <select class="form-select" name="preferred_location">
                                        <option value="">Chọn khu vực</option>
                                        <option value="Hoàng Mai">Hoàng Mai</option>
                                        <option value="Thanh Xuân">Thanh Xuân</option>
                                        <option value="Cầu Giấy">Cầu Giấy</option>
                                        <option value="Đống Đa">Đống Đa</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Yêu cầu đặc biệt</label>
                                    <textarea class="form-control" name="special_requirements" rows="4"
                                              placeholder="Mô tả chi tiết về yêu cầu đặc biệt cho giải đấu..."></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                        <label class="form-check-label" for="agreeTerms">
                                            Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản dịch vụ</a>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Gửi đăng ký
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Success Modal -->
<div class="modal fade" id="tournamentSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="success-icon mb-3">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h4 class="fw-bold text-success mb-3">Đăng ký thành công!</h4>
                <p class="text-muted mb-4">Chúng tôi đã nhận được đăng ký tổ chức giải đấu của bạn. Đội ngũ sẽ liên hệ trong vòng 24h.</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-success" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>Hoàn thành
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/tournament.js"></script>