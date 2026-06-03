<?php
require_once __DIR__ . '/includes/functions.php';

// Chặn admin truy cập trang web thường
// blockAdminFromPublic();

$filters = [
    'q' => $_GET['q'] ?? '',
    'location' => $_GET['location'] ?? '',
    'price' => $_GET['price'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'sort' => $_GET['sort'] ?? '',
];
$courts = getCourts($filters);
$locations = getLocations();

// Handle AJAX requests - return only court results
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: text/html; charset=utf-8');
    // Return only the court results HTML
    if (empty($courts)) {
        echo '<div class="col-12">
            <div class="no-results text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-muted opacity-50"></i>
                </div>
                <h4 class="text-muted mb-3">Không tìm thấy sân phù hợp</h4>
                <p class="text-muted mb-4">Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác</p>
                <button type="button" onclick="homepageSearch.resetSearch()" class="btn btn-primary">
                    <i class="fas fa-refresh me-2"></i>Xem tất cả sân
                </button>
            </div>
        </div>';
    }
    
    foreach ($courts as $court) {
        echo '<div class="col-md-6 col-lg-4">
            <div class="court-card-modern h-100 bg-white rounded-3 shadow-sm border-0 overflow-hidden">
                <div class="court-image-container position-relative">
                    <img src="' . escape($court['cover_image']) . '" 
                         class="court-image w-100" alt="' . escape($court['name']) . '">
                    <div class="court-status">
                        <span class="badge bg-success">Còn trống</span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <h5 class="court-name fw-bold mb-2 text-dark">' . escape($court['name']) . '</h5>
                    
                    <div class="court-info mb-3">
                        <div class="info-item d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            <span class="text-muted small">' . escape($court['location']) . '</span>
                        </div>
                        <div class="info-item d-flex align-items-center">
                            <i class="fas fa-money-bill text-muted me-2"></i>
                            <span class="price fw-bold text-success">
                                ' . number_format($court['price_per_hour']) . 'đ/giờ
                            </span>
                        </div>
                    </div>
                    
                    <p class="court-description text-muted small mb-3 lh-sm">
                        ' . escape(substr($court['description'], 0, 80)) . '...
                    </p>
                    
                    <div class="court-features mb-3">
                        <div class="d-flex gap-1 flex-wrap">
                            <span class="badge bg-light text-dark small">Có mái che</span>
                            <span class="badge bg-light text-dark small">Sân gỗ</span>
                            <span class="badge bg-light text-dark small">Điều hòa</span>
                        </div>
                    </div>
                    
                    <div class="court-actions d-flex gap-2">
                        <a href="court.php?id=' . $court['id'] . '" 
                           class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-eye me-1"></i>Chi tiết
                        </a>
                        <a href="booking-online.php" 
                           class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-calendar-plus me-1"></i>Đặt sân
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }
    exit; // Stop execution for AJAX requests
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="features-section py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="h4 fw-bold text-dark mb-2">Tại sao chọn chúng tôi?</h2>
                <p class="text-muted">Trải nghiệm đặt sân cầu lông tuyệt vời nhất</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-modern text-center p-4 h-100 bg-white rounded-3 shadow-sm border-0">
                    <div class="feature-icon-modern mb-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="fas fa-bolt text-primary fa-2x"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 text-dark">Đặt nhanh</h5>
                    <p class="text-muted mb-3">Chỉ <span class="text-primary fw-bold">3 bước</span> để hoàn tất đặt sân trong vòng <span class="text-primary fw-bold">2 phút</span>.</p>
                    <div class="feature-process">
                        <small class="text-primary fw-bold">
                            <i class="fas fa-search me-1"></i>Tìm → 
                            <i class="fas fa-clock me-1"></i>Chọn giờ → 
                            <i class="fas fa-credit-card me-1"></i>Thanh toán
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-modern text-center p-4 h-100 bg-white rounded-3 shadow-sm border-0">
                    <div class="feature-icon-modern mb-3">
                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="fas fa-map-marked-alt text-success fa-2x"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 text-dark">Đa dạng sân</h5>
                    <p class="text-muted mb-3">Hơn <span class="text-success fw-bold">50+ sân</span> chất lượng cao tại <span class="text-success fw-bold">8 quận/huyện</span> Hà Nội.</p>
                    <div class="feature-process">
                        <small class="text-success fw-bold">
                            <i class="fas fa-check-circle me-1"></i>Sân mới, hiện đại, có mái che
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-modern text-center p-4 h-100 bg-white rounded-3 shadow-sm border-0">
                    <div class="feature-icon-modern mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="fas fa-calendar-check text-info fa-2x"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 text-dark">Lịch rõ ràng</h5>
                    <p class="text-muted mb-3">Xem <span class="text-info fw-bold">lịch trống 24/7</span> theo giờ và tránh <span class="text-info fw-bold">chồng lịch 100%</span>.</p>
                    <div class="feature-process">
                        <small class="text-info fw-bold">
                            <i class="fas fa-sync-alt me-1"></i>Cập nhật thời gian thực
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-modern text-center p-4 h-100 bg-white rounded-3 shadow-sm border-0">
                    <div class="feature-icon-modern mb-3">
                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="fas fa-credit-card text-warning fa-2x"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 text-dark">Thanh toán linh hoạt</h5>
                    <p class="text-muted mb-3">Thanh toán <span class="text-warning fw-bold">online ngay</span> qua MoMo/VNPay hoặc <span class="text-warning fw-bold">trả tại chỗ</span>.</p>
                    <div class="feature-process">
                        <small class="text-warning fw-bold">
                            <i class="fas fa-shield-alt me-1"></i>An toàn, bảo mật 100%
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="search-section py-5" id="search">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="h4 fw-bold text-dark mb-2">Tìm sân cầu lông</h2>
                <p class="text-muted">Sử dụng bộ lọc để tìm sân phù hợp với bạn</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="search-card bg-white rounded-3 shadow-sm border p-4">
                    <form id="searchForm">
                        <!-- Quick Search Row -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-search text-primary me-2"></i>Tìm kiếm
                                </label>
                                <input type="search" name="q" class="form-control" id="searchInput"
                                       value="<?php echo escape($filters['q']); ?>" 
                                       placeholder="Tên sân, phường/xã...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-map-marker-alt text-success me-2"></i>Khu vực
                                </label>
                                <input list="locationList" type="text" name="location" class="form-control" id="locationInput"
                                       value="<?php echo escape($filters['location']); ?>" 
                                       placeholder="Chọn phường/xã...">
                                <datalist id="locationList">
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo escape($location); ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-money-bill text-warning me-2"></i>Mức giá
                                </label>
                                <select name="price" class="form-select" id="priceSelect">
                                    <option value="">Tất cả mức giá</option>
                                    <option value="low" <?php echo $filters['price'] === 'low' ? 'selected' : ''; ?>>Dưới 100k/giờ</option>
                                    <option value="mid" <?php echo $filters['price'] === 'mid' ? 'selected' : ''; ?>>100k - 150k/giờ</option>
                                    <option value="high" <?php echo $filters['price'] === 'high' ? 'selected' : ''; ?>>Trên 150k/giờ</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Advanced Filters -->
                        <div class="advanced-filters border-top pt-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Giá tối thiểu</label>
                                    <input type="number" name="min_price" min="0" class="form-control" id="minPriceInput"
                                           value="<?php echo escape($filters['min_price']); ?>" 
                                           placeholder="VD: 80000">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Giá tối đa</label>
                                    <input type="number" name="max_price" min="0" class="form-control" id="maxPriceInput"
                                           value="<?php echo escape($filters['max_price']); ?>" 
                                           placeholder="VD: 200000">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">Sắp xếp theo</label>
                                    <select name="sort" class="form-select" id="sortSelect">
                                        <option value="">Mặc định</option>
                                        <option value="price_asc" <?php echo $filters['sort'] === 'price_asc' ? 'selected' : ''; ?>>Giá thấp → cao</option>
                                        <option value="price_desc" <?php echo $filters['sort'] === 'price_desc' ? 'selected' : ''; ?>>Giá cao → thấp</option>
                                        <option value="newest" <?php echo $filters['sort'] === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-times me-2"></i>Xóa bộ lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Search Actions -->
                        <div class="text-center">
                            <button type="submit" id="searchBtn" class="btn btn-primary btn-lg px-5 rounded-pill">
                                <i class="fas fa-search me-2"></i>Tìm sân
                            </button>
                            <a href="map.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill ms-2">
                                <i class="fas fa-map me-2"></i>Xem bản đồ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Court Results -->
    <div class="container py-4">
        <div id="courtsResults" class="row g-4">
            <?php if (empty($courts)): ?>
                <div class="col-12">
                    <div class="no-results text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-search fa-4x text-muted opacity-50"></i>
                        </div>
                        <h4 class="text-muted mb-3">Không tìm thấy sân phù hợp</h4>
                        <p class="text-muted mb-4">Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác</p>
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>Xem tất cả sân
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php foreach ($courts as $court): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="court-card-modern h-100 bg-white rounded-3 shadow-sm border-0 overflow-hidden">
                        <div class="court-image-container position-relative">
                            <img src="<?php echo escape($court['cover_image']); ?>" 
                                 class="court-image w-100" alt="<?php echo escape($court['name']); ?>">
                            <div class="court-status">
                                <span class="badge bg-success">Còn trống</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            <h5 class="court-name fw-bold mb-2 text-dark"><?php echo escape($court['name']); ?></h5>
                            
                            <div class="court-info mb-3">
                                <div class="info-item d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    <span class="text-muted small"><?php echo escape($court['location']); ?></span>
                                </div>
                                <div class="info-item d-flex align-items-center">
                                    <i class="fas fa-money-bill text-muted me-2"></i>
                                    <span class="price fw-bold text-success">
                                        <?php echo number_format($court['price_per_hour']); ?>đ/giờ
                                    </span>
                                </div>
                            </div>
                            
                            <p class="court-description text-muted small mb-3 lh-sm">
                                <?php echo escape(substr($court['description'], 0, 80)); ?>...
                            </p>
                            
                            <div class="court-features mb-3">
                                <div class="d-flex gap-1 flex-wrap">
                                    <span class="badge bg-light text-dark small">Có mái che</span>
                                    <span class="badge bg-light text-dark small">Sân gỗ</span>
                                    <span class="badge bg-light text-dark small">Điều hòa</span>
                                </div>
                            </div>
                            
                            <div class="court-actions d-flex gap-2">
                                <a href="court.php?id=<?php echo $court['id']; ?>" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>Chi tiết
                                </a>
                                <a href="booking-online.php" 
                                   class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-calendar-plus me-1"></i>Đặt sân
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Map Preview Section -->
<section class="map-preview-section py-5 bg-light" id="map">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="map-preview-content">
                    <h2 class="h3 fw-bold mb-3">🗺️ Khám phá bản đồ sân cầu lông</h2>
                    <p class="text-muted mb-4">Tìm kiếm và khám phá các sân cầu lông gần bạn trên bản đồ tương tác. Xem vị trí chính xác, so sánh khoảng cách và chọn sân phù hợp nhất.</p>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-small bg-primary text-white rounded-circle me-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Định vị chính xác</h6>
                                    <small class="text-muted">Vị trí GPS của từng sân</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-small bg-success text-white rounded-circle me-3">
                                    <i class="fas fa-route"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Chỉ đường</h6>
                                    <small class="text-muted">Tích hợp Google Maps</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-small bg-info text-white rounded-circle me-3">
                                    <i class="fas fa-filter"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Lọc thông minh</h6>
                                    <small class="text-muted">Theo khoảng cách & giá</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="feature-item d-flex align-items-center">
                                <div class="feature-icon-small bg-warning text-white rounded-circle me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Trạng thái thời gian thực</h6>
                                    <small class="text-muted">Sân trống, đầy, ít slot</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <a href="map.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-map me-2"></i>Mở bản đồ
                        </a>
                        <a href="map.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-search-location me-2"></i>Tìm sân gần tôi
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="map-preview-image position-relative">
                    <div class="map-mockup rounded-4 shadow-lg overflow-hidden">
                        <div class="map-mockup-header bg-primary text-white p-3">
                            <div class="d-flex align-items-center">
                                <div class="mockup-controls d-flex gap-1 me-3">
                                    <div class="mockup-dot bg-danger"></div>
                                    <div class="mockup-dot bg-warning"></div>
                                    <div class="mockup-dot bg-success"></div>
                                </div>
                                <small class="fw-bold">Bản đồ sân cầu lông Hà Nội</small>
                            </div>
                        </div>
                        <div class="map-mockup-body bg-light position-relative" style="height: 300px;">
                            <!-- Mockup map content -->
                            <div class="position-absolute w-100 h-100" style="background: linear-gradient(45deg, #e3f2fd 25%, transparent 25%), linear-gradient(-45deg, #e3f2fd 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e3f2fd 75%), linear-gradient(-45deg, transparent 75%, #e3f2fd 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px; opacity: 0.1;"></div>
                            
                            <!-- Mockup markers -->
                            <div class="position-absolute" style="top: 20%; left: 30%;">
                                <div class="mockup-marker bg-success"></div>
                            </div>
                            <div class="position-absolute" style="top: 40%; left: 60%;">
                                <div class="mockup-marker bg-warning"></div>
                            </div>
                            <div class="position-absolute" style="top: 60%; left: 25%;">
                                <div class="mockup-marker bg-danger"></div>
                            </div>
                            <div class="position-absolute" style="top: 30%; left: 70%;">
                                <div class="mockup-marker bg-success"></div>
                            </div>
                            <div class="position-absolute" style="top: 70%; left: 50%;">
                                <div class="mockup-marker bg-primary"></div>
                            </div>
                            
                            <!-- Mockup popup -->
                            <div class="position-absolute" style="top: 35%; left: 45%;">
                                <div class="mockup-popup bg-white shadow-sm rounded p-2" style="width: 120px;">
                                    <div class="fw-bold" style="font-size: 0.7rem;">Sân ABC</div>
                                    <div class="text-muted" style="font-size: 0.6rem;">120,000đ/giờ</div>
                                    <div class="badge bg-success" style="font-size: 0.5rem;">Còn trống</div>
                                </div>
                            </div>
                            
                            <!-- Center overlay -->
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="text-center">
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-map fa-2x"></i>
                                    </div>
                                    <div class="mt-2">
                                        <small class="fw-bold text-primary">Bản đồ tương tác</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating stats -->
                    <div class="position-absolute top-0 end-0 translate-middle">
                        <div class="bg-white rounded-pill shadow px-3 py-2">
                            <small class="fw-bold text-success">50+ sân</small>
                        </div>
                    </div>
                    <div class="position-absolute bottom-0 start-0 translate-middle">
                        <div class="bg-white rounded-pill shadow px-3 py-2">
                            <small class="fw-bold text-primary">8 quận/huyện</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="locations-section py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="h4 fw-bold text-dark mb-2">Khu vực phủ sóng</h2>
                <p class="text-muted">Chọn khu vực để tìm sân gần bạn nhất</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="locations-grid d-flex flex-wrap justify-content-center gap-2">
                    <?php foreach ($locations as $location): ?>
                        <a href="index.php?location=<?php echo urlencode($location); ?>" 
                           class="location-btn btn btn-outline-primary btn-sm rounded-pill">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo escape($location); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="map.php" class="btn btn-primary rounded-pill">
                        <i class="fas fa-map me-2"></i>Xem tất cả trên bản đồ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/homepage-search.js"></script>