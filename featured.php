<?php
require_once __DIR__ . '/includes/functions.php';

// Chặn admin truy cập trang web thường
blockAdminFromPublic();

// Get filter parameters
$filters = [
    'category' => $_GET['category'] ?? '',
    'rating'   => $_GET['rating']   ?? '',
    'price'    => $_GET['price']    ?? '',
    'location' => $_GET['location'] ?? ''
];

$courts    = getCourts($filters);
$locations = getLocations();

// getCourts() now filters by DB category column
$featuredCourts = $courts;

// Meta thông tin cho từng category
$categoryMeta = [
    'premium' => ['label' => 'Sân cao cấp',   'icon' => 'fa-crown',    'desc' => 'Những sân chất lượng cao cấp, tiện nghi đầy đủ'],
    'popular' => ['label' => 'Phổ biến nhất', 'icon' => 'fa-fire',     'desc' => 'Sân được đặt nhiều nhất và đánh giá tốt nhất'],
    'new'     => ['label' => 'Mới nhất',       'icon' => 'fa-sparkles', 'desc' => 'Sân mới được thêm vào hệ thống'],
    'promo'   => ['label' => 'Khuyến mãi',     'icon' => 'fa-percent',  'desc' => 'Sân có giá ưu đãi, tiết kiệm chi phí'],
    ''        => ['label' => 'Nổi bật',        'icon' => 'fa-star',     'desc' => 'Những sân được đánh giá cao và yêu thích nhất'],
];
$currentMeta = $categoryMeta[$filters['category']] ?? $categoryMeta[''];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Featured Page Header -->
<section class="bg-warning text-dark py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-2">
                    <i class="fas <?php echo $currentMeta['icon']; ?> me-2"></i>
                    <?php echo escape($currentMeta['label']); ?>
                </h1>
                <p class="mb-0 opacity-75"><?php echo escape($currentMeta['desc']); ?></p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="index.php" class="btn btn-dark">
                    <i class="fas fa-arrow-left me-2"></i>Về trang chủ
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Advanced Filter Bar -->
<section class="bg-light py-3 border-bottom">
    <div class="container">
        <form class="row g-2 align-items-end" method="get" action="featured.php" id="featuredSearchForm">
            <div class="col-sm-6 col-md-3">
                <label class="form-label fw-bold">Danh mục</label>
                <select name="category" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="premium" <?php echo $filters['category'] === 'premium' ? 'selected' : ''; ?>>👑 Sân cao cấp</option>
                    <option value="popular" <?php echo $filters['category'] === 'popular' ? 'selected' : ''; ?>>🔥 Phổ biến nhất</option>
                    <option value="new"     <?php echo $filters['category'] === 'new'     ? 'selected' : ''; ?>>✨ Mới nhất</option>
                    <option value="promo"   <?php echo $filters['category'] === 'promo'   ? 'selected' : ''; ?>>% Khuyến mãi</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label fw-bold">Đánh giá</label>
                <select name="rating" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="5" <?php echo $filters['rating'] === '5' ? 'selected' : ''; ?>>5 sao</option>
                    <option value="4" <?php echo $filters['rating'] === '4' ? 'selected' : ''; ?>>4+ sao</option>
                    <option value="3" <?php echo $filters['rating'] === '3' ? 'selected' : ''; ?>>3+ sao</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label fw-bold">Khu vực</label>
                <select name="location" class="form-select">
                    <option value="">Tất cả</option>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?php echo escape($location); ?>" <?php echo $filters['location'] === $location ? 'selected' : ''; ?>>
                            <?php echo escape($location); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label fw-bold">Mức giá</label>
                <select name="price" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="low" <?php echo $filters['price'] === 'low' ? 'selected' : ''; ?>>Dưới 100k</option>
                    <option value="mid" <?php echo $filters['price'] === 'mid' ? 'selected' : ''; ?>>100k - 150k</option>
                    <option value="high" <?php echo $filters['price'] === 'high' ? 'selected' : ''; ?>>Trên 150k</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label opacity-0">Lọc</label>
                <button type="submit" class="btn btn-warning w-100">
                    <i class="fas fa-filter me-2"></i>Lọc
                </button>
            </div>
            <div class="col-sm-6 col-md-1">
                <label class="form-label opacity-0">Reset</label>
                <a href="featured.php" class="btn btn-outline-secondary w-100" title="Xóa bộ lọc">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</section>

<!-- Main Featured Section -->
<section class="featured-page-section">
    <div class="container-fluid">
        <div class="row g-0">
            <!-- Featured Courts Grid -->
            <div class="col-lg-9">
                <div class="featured-content p-4">
                    <!-- Stats Bar -->
                    <div class="stats-bar mb-4 p-3 bg-light rounded">
                        <div class="row text-center">
                            <div class="col-6 col-md-3">
                                <div class="stat-item">
                                    <div class="h4 fw-bold text-warning mb-1"><?php echo count($featuredCourts); ?></div>
                                    <small class="text-muted"><?php echo escape($currentMeta['label']); ?></small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-item">
                                    <?php
                                    $avgRating = 0;
                                    if (!empty($featuredCourts)) {
                                        $ratings = array_column(array_values($featuredCourts), 'avg_rating');
                                        $avgRating = round(array_sum($ratings) / count($ratings), 1);
                                    }
                                    ?>
                                    <div class="h4 fw-bold text-success mb-1"><?php echo $avgRating ?: 'N/A'; ?></div>
                                    <small class="text-muted">Đánh giá TB</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-item">
                                    <div class="h4 fw-bold text-info mb-1">2,500+</div>
                                    <small class="text-muted">Lượt đặt</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-item">
                                    <div class="h4 fw-bold text-primary mb-1">95%</div>
                                    <small class="text-muted">Hài lòng</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Courts Grid -->
                    <div class="row g-4" id="featuredCourtsList">
                        <?php if (empty($featuredCourts)): ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-search fa-2x mb-3"></i>
                                    <h5>Không tìm thấy sân nổi bật phù hợp</h5>
                                    <p class="mb-0">Vui lòng thử lại với bộ lọc khác hoặc xem tất cả sân.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php foreach ($featuredCourts as $court): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 shadow-sm border-0 featured-court-card">
                                    <div class="position-relative">
                                        <img src="<?php echo escape($court['cover_image']); ?>" class="card-img-top" alt="<?php echo escape($court['name']); ?>">
                                        
                                        <!-- Badges -->
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <?php
                                            $badgeMap = [
                                                'premium' => ['bg-warning text-dark', 'fa-crown',    'Cao cấp'],
                                                'popular' => ['bg-danger text-white',  'fa-fire',     'Phổ biến'],
                                                'new'     => ['bg-info text-white',    'fa-sparkles', 'Mới nhất'],
                                                'promo'   => ['bg-success text-white', 'fa-percent',  'Khuyến mãi'],
                                                ''        => ['bg-warning text-dark',  'fa-star',     'Nổi bật'],
                                            ];
                                            $b = $badgeMap[$filters['category']] ?? $badgeMap[''];
                                            ?>
                                            <span class="badge <?php echo $b[0]; ?> mb-1">
                                                <i class="fas <?php echo $b[1]; ?> me-1"></i><?php echo $b[2]; ?>
                                            </span>
                                            <br>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Còn trống
                                            </span>
                                        </div>
                                        
                                        <!-- Price Badge -->
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-dark">
                                                <?php echo number_format($court['price_per_hour']); ?>đ/h
                                            </span>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="position-absolute bottom-0 end-0 m-2">
                                            <div class="btn-group-vertical">
                                                <button class="btn btn-light btn-sm" title="Yêu thích">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                                <button class="btn btn-light btn-sm" title="Chia sẻ">
                                                    <i class="fas fa-share"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold"><?php echo escape($court['name']); ?></h5>
                                        
                                        <div class="court-meta mb-2">
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                <?php echo escape($court['location']); ?>
                                            </p>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-clock text-info me-2"></i>
                                                6:00 - 22:00
                                            </p>
                                        </div>
                                        
                                        <!-- Rating -->
                                        <div class="rating-section mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="rating">
                                                    <?php
                                                    $r = (float)($court['avg_rating'] ?? 0);
                                                    echo renderStars($r);
                                                    ?>
                                                    <small class="text-muted ms-1">(<?php echo $r > 0 ? number_format($r, 1) : 'Chưa có'; ?>)</small>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-comments me-1"></i><?php echo (int)($court['review_count'] ?? 0); ?> đánh giá
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <!-- Features -->
                                        <div class="features mb-3">
                                            <span class="badge bg-light text-dark me-1">Có mái che</span>
                                            <span class="badge bg-light text-dark me-1">Sân gỗ</span>
                                            <span class="badge bg-light text-dark">Điều hòa</span>
                                        </div>
                                        
                                        <p class="card-text text-muted flex-grow-1"><?php echo escape($court['description']); ?></p>
                                        
                                        <!-- Actions -->
                                        <div class="mt-auto">
                                            <div class="d-grid gap-2">
                                                <a href="booking-online.php?court_id=<?php echo $court['id']; ?>" 
                                                   class="btn btn-warning fw-bold">
                                                    <i class="fas fa-calendar-check me-2"></i>Đặt sân ngay
                                                </a>
                                                <div class="d-flex gap-2">
                                                    <?php
                                                    $phone = $court['phone'] ?? '0901234500';
                                                    $phoneDisplay = preg_replace('/(\d{4})(\d{3})(\d{3})/', '$1.$2.$3', $phone);
                                                    ?>
                                                    <a href="tel:<?php echo $phone; ?>"
                                                       class="btn btn-outline-primary btn-sm flex-fill"
                                                       onclick="return confirmCall('<?php echo escape($court['name']); ?>', '<?php echo $phone; ?>', '<?php echo $phoneDisplay; ?>')">
                                                        <i class="fas fa-phone me-1"></i>Gọi ngay
                                                    </a>
                                                    <a href="map.php?court_id=<?php echo $court['id']; ?>&name=<?php echo urlencode($court['name']); ?>&lat=<?php echo $court['latitude'] ?? '21.0285'; ?>&lng=<?php echo $court['longitude'] ?? '105.8542'; ?>" 
                                                       class="btn btn-outline-info btn-sm flex-fill">
                                                        <i class="fas fa-map-marker-alt me-1"></i>Chỉ đường
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Load More -->
                    <?php if (count($featuredCourts) >= 6): ?>
                        <div class="text-center mt-4">
                            <button class="btn btn-outline-warning btn-lg" id="loadMoreBtn">
                                <i class="fas fa-plus me-2"></i>Xem thêm sân nổi bật
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="featured-sidebar bg-light h-100 p-4">
                    <!-- Quick Categories -->
                    <div class="sidebar-section mb-4">
                        <h6 class="fw-bold mb-3">🏆 Danh mục nổi bật</h6>
                        <div class="d-grid gap-2">
                            <?php
                            $sidebarCategories = [
                                'premium' => ['fa-crown',    'Sân cao cấp'],
                                'popular' => ['fa-fire',     'Phổ biến nhất'],
                                'new'     => ['fa-sparkles', 'Mới nhất'],
                                'promo'   => ['fa-percent',  'Khuyến mãi'],
                            ];
                            foreach ($sidebarCategories as $catKey => $catInfo):
                                $isActive = $filters['category'] === $catKey;
                            ?>
                            <a href="featured.php?category=<?php echo $catKey; ?>"
                               class="btn btn-sm text-start <?php echo $isActive ? 'btn-warning fw-bold' : 'btn-outline-warning'; ?>">
                                <i class="fas <?php echo $catInfo[0]; ?> me-2"></i><?php echo $catInfo[1]; ?>
                                <?php if ($isActive): ?>
                                    <i class="fas fa-check ms-auto float-end mt-1"></i>
                                <?php endif; ?>
                            </a>
                            <?php endforeach; ?>
                            <?php if ($filters['category'] !== ''): ?>
                            <a href="featured.php" class="btn btn-outline-secondary btn-sm text-start">
                                <i class="fas fa-times me-2"></i>Xem tất cả
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Top Rated -->
                    <div class="sidebar-section mb-4">
                        <h6 class="fw-bold mb-3">⭐ Top đánh giá</h6>
                        <?php
                        // Lấy top 3 theo avg_rating từ toàn bộ sân
                        $allCourts = getCourts(['sort' => 'rating']);
                        $topRated  = array_slice($allCourts, 0, 3);
                        foreach ($topRated as $court):
                            $r = (float)($court['avg_rating'] ?? 0);
                        ?>
                            <div class="top-rated-item mb-3 p-2 bg-white rounded">
                                <a href="booking-online.php?court_id=<?php echo $court['id']; ?>" class="text-decoration-none text-dark">
                                    <div class="d-flex">
                                        <img src="<?php echo escape($court['cover_image']); ?>" 
                                             class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;" 
                                             alt="<?php echo escape($court['name']); ?>">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small"><?php echo escape($court['name']); ?></div>
                                            <div class="text-muted small"><?php echo escape($court['location']); ?></div>
                                            <div class="small">
                                                <?php echo renderStars($r); ?>
                                                <span class="text-muted ms-1"><?php echo $r > 0 ? number_format($r, 1) : 'Mới'; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Special Offers -->
                    <div class="sidebar-section">
                        <h6 class="fw-bold mb-3">🎁 Ưu đãi đặc biệt</h6>
                        <?php
                        // Tạo bảng nếu chưa có
                        $mysqli->query("CREATE TABLE IF NOT EXISTS promotions (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            title VARCHAR(150) NOT NULL,
                            description VARCHAR(255),
                            color_from VARCHAR(20) DEFAULT '#f472b6',
                            color_to VARCHAR(20) DEFAULT '#ef4444',
                            text_color VARCHAR(20) DEFAULT '#fff',
                            discount_pct TINYINT DEFAULT 0,
                            time_start TIME DEFAULT NULL,
                            time_end TIME DEFAULT NULL,
                            apply_weekend TINYINT DEFAULT 0,
                            apply_newuser TINYINT DEFAULT 0,
                            status TINYINT DEFAULT 1,
                            sort_order INT DEFAULT 0,
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                        $promos = $mysqli->query(
                            'SELECT * FROM promotions WHERE status=1 ORDER BY sort_order ASC, id DESC'
                        )->fetch_all(MYSQLI_ASSOC);

                        if (empty($promos)):
                            // Fallback mặc định nếu chưa có data
                            $promos = [
                                ['title'=>'Giảm 30% Happy Hour',  'description'=>'14:00 - 17:00 hàng ngày',  'color_from'=>'#f472b6','color_to'=>'#ef4444','text_color'=>'#1a1a2e','discount_pct'=>30],
                                ['title'=>'Tích điểm x2',          'description'=>'Cuối tuần và lễ tết',       'color_from'=>'#4ade80','color_to'=>'#22c55e','text_color'=>'#fff','discount_pct'=>0],
                                ['title'=>'Miễn phí lần đầu',      'description'=>'Cho thành viên mới',        'color_from'=>'#38bdf8','color_to'=>'#06b6d4','text_color'=>'#fff','discount_pct'=>100],
                            ];
                        endif;
                        ?>
                        <?php foreach ($promos as $promo): ?>
                        <div class="offer-card p-3 rounded mb-3"
                             style="background:linear-gradient(135deg,<?php echo escape($promo['color_from']); ?>,<?php echo escape($promo['color_to']); ?>);color:<?php echo escape($promo['text_color']); ?>;">
                            <div class="fw-bold"><?php echo escape($promo['title']); ?></div>
                            <?php if (!empty($promo['description'])): ?>
                                <small style="opacity:.85;"><?php echo escape($promo['description']); ?></small>
                            <?php endif; ?>
                            <?php if (!empty($promo['discount_pct']) && $promo['discount_pct'] > 0): ?>
                                <div class="mt-1">
                                    <span style="background:rgba(255,255,255,.25);border-radius:6px;padding:2px 8px;font-size:.7rem;font-weight:700;">
                                        -<?php echo $promo['discount_pct']; ?>%
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>

                        <?php if (isAdmin()): ?>
                        <a href="../admin/promotions.php" class="btn btn-sm btn-outline-warning w-100 mt-1">
                            <i class="fas fa-cog me-1"></i>Quản lý ưu đãi
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Featured page specific scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load more functionality
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            // Simulate loading more courts
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tải...';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check me-2"></i>Đã tải tất cả';
                this.disabled = true;
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-success');
            }, 1500);
        });
    }
    
    // Heart button functionality
    document.querySelectorAll('.fa-heart').forEach(heart => {
        heart.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            heart.classList.toggle('text-danger');
            if (heart.classList.contains('text-danger')) {
                heart.classList.remove('far');
                heart.classList.add('fas');
            } else {
                heart.classList.remove('fas');
                heart.classList.add('far');
            }
        });
    });
});

// Hàm xác nhận gọi điện
function confirmCall(courtName, phone, phoneDisplay) {
    document.getElementById('callCourtName').textContent = courtName;
    document.getElementById('callPhoneDisplay').textContent = phoneDisplay;
    document.getElementById('callPhoneLink').href = 'tel:' + phone;
    document.getElementById('callPhoneLink').setAttribute('data-phone', phone);
    new bootstrap.Modal(document.getElementById('callModal')).show();
    return false; // Ngăn href tel: chạy trước khi modal hiện
}
</script>

<!-- Modal xác nhận gọi điện -->
<div class="modal fade" id="callModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
            <!-- Header xanh gradient -->
            <div style="background:linear-gradient(135deg,#0d6efd,#0dcaf0);padding:1.4rem 1.5rem;text-align:center;color:#fff;">
                <div style="width:60px;height:60px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .8rem;">
                    <i class="fas fa-phone fa-xl"></i>
                </div>
                <h5 class="fw-bold mb-1">Gọi cho sân</h5>
                <div id="callCourtName" style="opacity:.85;font-size:.9rem;"></div>
            </div>

            <!-- Body -->
            <div style="padding:1.5rem;text-align:center;">
                <div style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Số điện thoại liên hệ:</div>
                <div id="callPhoneDisplay"
                     style="font-size:1.8rem;font-weight:900;color:#0d6efd;letter-spacing:2px;margin-bottom:.5rem;"></div>
                <div style="font-size:.78rem;color:#9ca3af;margin-bottom:1.5rem;">
                    <i class="fas fa-clock me-1"></i>Hỗ trợ 6:00 – 22:00 hàng ngày
                </div>

                <div class="d-grid gap-2">
                    <a id="callPhoneLink" href="#"
                       class="btn btn-primary btn-lg fw-bold"
                       style="border-radius:14px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border:none;"
                       onclick="document.getElementById('callModal').querySelector('.btn-close').click()">
                        <i class="fas fa-phone me-2"></i>Gọi ngay
                    </a>
                    <button class="btn btn-outline-secondary"
                            style="border-radius:14px;"
                            data-bs-dismiss="modal">
                        Huỷ
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding:.7rem;background:#f9fafb;text-align:center;font-size:.75rem;color:#9ca3af;border-top:1px solid #f0f0f0;">
                <i class="fas fa-shield-alt me-1 text-success"></i>
                Cuộc gọi trực tiếp tới sân — không qua trung gian
            </div>
        </div>
    </div>
</div>