<?php
require_once __DIR__ . '/includes/functions.php';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Training Page Header -->
<section class="bg-warning text-dark py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">
                    <i class="fas fa-graduation-cap me-2"></i>Đào tạo cầu lông
                </h1>
                <p class="mb-0 opacity-75">Từ cơ bản đến nâng cao - HLV chuyên nghiệp</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="discover.php" class="btn btn-dark">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Training Programs -->
<section class="training-programs py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Chương trình đào tạo</h2>
                <p class="text-muted">Lựa chọn khóa học phù hợp với trình độ của bạn</p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Beginner Course -->
            <div class="col-lg-4">
                <div class="training-card h-100 bg-white rounded-4 shadow-sm border">
                    <div class="training-image">
                        <img src="https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="card-img-top rounded-top" alt="Khóa cơ bản">
                        <div class="training-level">
                            <span class="badge bg-success">Cơ bản</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Khóa cầu lông cơ bản</h5>
                        <div class="training-info mb-3">
                            <div class="info-item mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span>12 buổi (3 tháng)</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-users text-info me-2"></i>
                                <span>4-6 học viên/lớp</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <span>2 buổi/tuần</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-money-bill text-success me-2"></i>
                                <span class="fw-bold">1,800,000đ</span>
                            </div>
                        </div>
                        <div class="training-content mb-3">
                            <h6 class="fw-bold">Nội dung khóa học:</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>Kỹ thuật cầm vợt cơ bản</li>
                                <li><i class="fas fa-check text-success me-2"></i>Tư thế di chuyển</li>
                                <li><i class="fas fa-check text-success me-2"></i>Kỹ thuật đánh cầu</li>
                                <li><i class="fas fa-check text-success me-2"></i>Luật thi đấu cơ bản</li>
                            </ul>
                        </div>
                        <button class="btn btn-success w-100" data-course="beginner">
                            <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Intermediate Course -->
            <div class="col-lg-4">
                <div class="training-card h-100 bg-white rounded-4 shadow-sm border border-warning border-3 position-relative">
                    <div class="popular-badge">
                        <span class="badge bg-warning text-dark">Phổ biến</span>
                    </div>
                    <div class="training-image">
                        <img src="https://images.unsplash.com/photo-1544717297-fa95b6ee9643?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="card-img-top rounded-top" alt="Khóa trung cấp">
                        <div class="training-level">
                            <span class="badge bg-warning text-dark">Trung cấp</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Khóa cầu lông trung cấp</h5>
                        <div class="training-info mb-3">
                            <div class="info-item mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span>16 buổi (4 tháng)</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-users text-info me-2"></i>
                                <span>4-6 học viên/lớp</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <span>2 buổi/tuần</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-money-bill text-success me-2"></i>
                                <span class="fw-bold">2,800,000đ</span>
                            </div>
                        </div>
                        <div class="training-content mb-3">
                            <h6 class="fw-bold">Nội dung khóa học:</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>Kỹ thuật nâng cao</li>
                                <li><i class="fas fa-check text-success me-2"></i>Chiến thuật thi đấu</li>
                                <li><i class="fas fa-check text-success me-2"></i>Thể lực chuyên môn</li>
                                <li><i class="fas fa-check text-success me-2"></i>Thi đấu thực tế</li>
                            </ul>
                        </div>
                        <button class="btn btn-warning w-100" data-course="intermediate">
                            <i class="fas fa-star me-2"></i>Đăng ký ngay
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Course -->
            <div class="col-lg-4">
                <div class="training-card h-100 bg-white rounded-4 shadow-sm border">
                    <div class="training-image">
                        <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="card-img-top rounded-top" alt="Khóa nâng cao">
                        <div class="training-level">
                            <span class="badge bg-danger">Nâng cao</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Khóa cầu lông nâng cao</h5>
                        <div class="training-info mb-3">
                            <div class="info-item mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span>20 buổi (5 tháng)</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-users text-info me-2"></i>
                                <span>3-4 học viên/lớp</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <span>3 buổi/tuần</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-money-bill text-success me-2"></i>
                                <span class="fw-bold">4,500,000đ</span>
                            </div>
                        </div>
                        <div class="training-content mb-3">
                            <h6 class="fw-bold">Nội dung khóa học:</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>Kỹ thuật chuyên sâu</li>
                                <li><i class="fas fa-check text-success me-2"></i>Chiến thuật cao cấp</li>
                                <li><i class="fas fa-check text-success me-2"></i>Tâm lý thi đấu</li>
                                <li><i class="fas fa-check text-success me-2"></i>Chuẩn bị thi đấu</li>
                            </ul>
                        </div>
                        <button class="btn btn-danger w-100" data-course="advanced">
                            <i class="fas fa-trophy me-2"></i>Đăng ký ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Coaches Section -->
<section class="coaches-section py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Đội ngũ huấn luyện viên</h2>
                <p class="text-muted">HLV giàu kinh nghiệm và tận tâm</p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Coach 1 -->
            <div class="col-lg-4">
                <div class="coach-card bg-white rounded-4 shadow-sm p-4 text-center">
                    <div class="coach-avatar mb-3">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                             class="rounded-circle" alt="HLV Nguyễn Văn A">
                    </div>
                    <h5 class="fw-bold mb-2">HLV Nguyễn Văn A</h5>
                    <p class="text-muted mb-3">Chuyên gia kỹ thuật cơ bản</p>
                    <div class="coach-info mb-3">
                        <div class="info-badge mb-2">
                            <i class="fas fa-medal text-warning me-2"></i>
                            <span>15+ năm kinh nghiệm</span>
                        </div>
                        <div class="info-badge mb-2">
                            <i class="fas fa-certificate text-success me-2"></i>
                            <span>Chứng chỉ BWF Level 2</span>
                        </div>
                        <div class="info-badge">
                            <i class="fas fa-users text-info me-2"></i>
                            <span>500+ học viên</span>
                        </div>
                    </div>
                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#coach1Modal">
                        <i class="fas fa-info-circle me-2"></i>Xem chi tiết
                    </button>
                </div>
            </div>
            
            <!-- Coach 2 -->
            <div class="col-lg-4">
                <div class="coach-card bg-white rounded-4 shadow-sm p-4 text-center">
                    <div class="coach-avatar mb-3">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                             class="rounded-circle" alt="HLV Trần Thị B">
                    </div>
                    <h5 class="fw-bold mb-2">HLV Trần Thị B</h5>
                    <p class="text-muted mb-3">Chuyên gia chiến thuật</p>
                    <div class="coach-info mb-3">
                        <div class="info-badge mb-2">
                            <i class="fas fa-medal text-warning me-2"></i>
                            <span>12+ năm kinh nghiệm</span>
                        </div>
                        <div class="info-badge mb-2">
                            <i class="fas fa-certificate text-success me-2"></i>
                            <span>Chứng chỉ BWF Level 3</span>
                        </div>
                        <div class="info-badge">
                            <i class="fas fa-trophy text-danger me-2"></i>
                            <span>Cựu VĐV quốc gia</span>
                        </div>
                    </div>
                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#coach2Modal">
                        <i class="fas fa-info-circle me-2"></i>Xem chi tiết
                    </button>
                </div>
            </div>
            
            <!-- Coach 3 -->
            <div class="col-lg-4">
                <div class="coach-card bg-white rounded-4 shadow-sm p-4 text-center">
                    <div class="coach-avatar mb-3">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                             class="rounded-circle" alt="HLV Lê Văn C">
                    </div>
                    <h5 class="fw-bold mb-2">HLV Lê Văn C</h5>
                    <p class="text-muted mb-3">Chuyên gia thể lực</p>
                    <div class="coach-info mb-3">
                        <div class="info-badge mb-2">
                            <i class="fas fa-medal text-warning me-2"></i>
                            <span>10+ năm kinh nghiệm</span>
                        </div>
                        <div class="info-badge mb-2">
                            <i class="fas fa-certificate text-success me-2"></i>
                            <span>Chứng chỉ Fitness</span>
                        </div>
                        <div class="info-badge">
                            <i class="fas fa-dumbbell text-primary me-2"></i>
                            <span>Chuyên gia thể lực</span>
                        </div>
                    </div>
                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#coach3Modal">
                        <i class="fas fa-info-circle me-2"></i>Xem chi tiết
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Training Registration -->
<section class="training-registration py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-user-plus me-2"></i>Đăng ký khóa học
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="trainingForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Họ và tên *</label>
                                    <input type="text" class="form-control" name="student_name" required
                                           placeholder="Nhập họ và tên">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số điện thoại *</label>
                                    <input type="tel" class="form-control" name="phone" required
                                           placeholder="0123456789">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" name="email"
                                           placeholder="email@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tuổi *</label>
                                    <select class="form-select" name="age_group" required>
                                        <option value="">Chọn độ tuổi</option>
                                        <option value="6-12">6-12 tuổi</option>
                                        <option value="13-17">13-17 tuổi</option>
                                        <option value="18-30">18-30 tuổi</option>
                                        <option value="30+">Trên 30 tuổi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Khóa học *</label>
                                    <select class="form-select" name="course" required id="courseSelect">
                                        <option value="">Chọn khóa học</option>
                                        <option value="beginner">Cơ bản - 1,800,000đ</option>
                                        <option value="intermediate">Trung cấp - 2,800,000đ</option>
                                        <option value="advanced">Nâng cao - 4,500,000đ</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Trình độ hiện tại</label>
                                    <select class="form-select" name="current_level">
                                        <option value="">Chọn trình độ</option>
                                        <option value="beginner">Mới bắt đầu</option>
                                        <option value="basic">Biết cơ bản</option>
                                        <option value="intermediate">Trung bình</option>
                                        <option value="advanced">Khá</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thời gian học mong muốn</label>
                                    <select class="form-select" name="preferred_time">
                                        <option value="">Chọn thời gian</option>
                                        <option value="morning">Sáng (6:00-9:00)</option>
                                        <option value="afternoon">Chiều (14:00-17:00)</option>
                                        <option value="evening">Tối (18:00-21:00)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">HLV mong muốn</label>
                                    <select class="form-select" name="preferred_coach">
                                        <option value="">Chọn HLV</option>
                                        <option value="coach1">HLV Nguyễn Văn A</option>
                                        <option value="coach2">HLV Trần Thị B</option>
                                        <option value="coach3">HLV Lê Văn C</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Mục tiêu học tập</label>
                                    <textarea class="form-control" name="learning_goals" rows="3"
                                              placeholder="Mô tả mục tiêu và mong muốn của bạn khi học cầu lông..."></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agreeTrainingTerms" required>
                                        <label class="form-check-label" for="agreeTrainingTerms">
                                            Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#trainingTermsModal">điều khoản khóa học</a>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-warning btn-lg w-100">
                                        <i class="fas fa-graduation-cap me-2"></i>Đăng ký khóa học
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
<div class="modal fade" id="trainingSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="success-icon mb-3">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h4 class="fw-bold text-success mb-3">Đăng ký thành công!</h4>
                <p class="text-muted mb-4">Chúng tôi đã nhận được đăng ký khóa học của bạn. Đội ngũ sẽ liên hệ để xác nhận lịch học.</p>
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

<script src="assets/js/training.js"></script>