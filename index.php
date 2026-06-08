<?php
require_once __DIR__ . '/includes/functions.php';

// Chặn admin truy cập trang web thường
blockAdminFromPublic();

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

// Handle AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: text/html; charset=utf-8');
    if (empty($courts)) {
        echo '<div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted opacity-50 mb-3 d-block"></i>
                <h5 class="text-muted">Không tìm thấy sân phù hợp</h5>
                <p class="text-muted small">Hãy thử điều chỉnh bộ lọc</p>
                <button onclick="homepageSearch.resetSearch()" class="btn btn-primary btn-sm">
                    Xem tất cả sân
                </button>
            </div>
        </div>';
    }
    foreach ($courts as $court) {
        $phone = $court['phone'] ?? '0968073500';
        echo '<div class="col-md-6 col-lg-4">
            <div class="court-card-v2">
                <div class="court-img-wrap">
                    <img src="' . escape($court['cover_image']) . '" alt="' . escape($court['name']) . '">
                    <span class="court-badge-status">Còn trống</span>
                    <span class="court-badge-price">' . number_format($court['price_per_hour']) . 'đ/h</span>
                </div>
                <div class="court-body">
                    <h5 class="court-title">' . escape($court['name']) . '</h5>
                    <div class="court-meta">
                        <span><i class="fas fa-map-marker-alt"></i>' . escape($court['location']) . '</span>
                        <span><i class="fas fa-clock"></i>6:00–22:00</span>
                    </div>
                    <div class="court-tags">
                        <span>Mái che</span><span>Sân gỗ</span><span>Điều hòa</span>
                    </div>
                    <div class="court-footer">
                        <a href="booking-online.php?court_id=' . $court['id'] . '" class="btn-book">
                            <i class="fas fa-calendar-check me-1"></i>Đặt sân
                        </a>
                        <a href="tel:' . $phone . '" class="btn-call" title="Gọi ngay">
                            <i class="fas fa-phone"></i>
                        </a>
                        <a href="map.php?court_id=' . $court['id'] . '&name=' . urlencode($court['name']) . '&lat=' . ($court['latitude'] ?? '21.0285') . '&lng=' . ($court['longitude'] ?? '105.8542') . '" class="btn-map" title="Chỉ đường">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
/* ===== HERO SECTION ===== */
.hero-section {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 5rem 0 6rem;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute;
    top: -100px; right: -100px;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(102,126,234,.15) 0%, transparent 70%);
    border-radius: 50%;
}
.hero-section::after {
    content: '';
    position: absolute;
    bottom: -80px; left: -80px;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(40,167,69,.1) 0%, transparent 70%);
    border-radius: 50%;
}
.hero-tag {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(102,126,234,.2); border: 1px solid rgba(102,126,234,.4);
    color: #a5b4fc; padding: 5px 14px; border-radius: 50px;
    font-size: .8rem; font-weight: 600; margin-bottom: 1.5rem;
}
.hero-title {
    font-size: clamp(2rem, 5vw, 3.2rem);
    font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 1rem;
}
.hero-title .gradient-text {
    background: linear-gradient(135deg, #667eea, #a78bfa);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.hero-desc { color: rgba(255,255,255,.65); font-size: 1.05rem; margin-bottom: 2rem; max-width: 480px; }
.hero-stats { display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 2.5rem; }
.hero-stat .n { font-size: 1.8rem; font-weight: 900; color: #a5b4fc; }
.hero-stat .l { font-size: .72rem; color: rgba(255,255,255,.5); }
.btn-hero-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff; border: none; border-radius: 14px;
    padding: .85rem 2rem; font-weight: 700; font-size: 1rem;
    box-shadow: 0 8px 25px rgba(102,126,234,.4);
    transition: all .2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
}
.btn-hero-primary:hover { transform: translateY(-2px); color: #fff; box-shadow: 0 12px 35px rgba(102,126,234,.5); }
.btn-hero-secondary {
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    color: #fff; border-radius: 14px;
    padding: .85rem 2rem; font-weight: 600; font-size: 1rem;
    transition: all .2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
}
.btn-hero-secondary:hover { background: rgba(255,255,255,.18); color: #fff; }

/* ===== SEARCH BAR ===== */
.search-floating {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 8px 30px rgba(0,0,0,.12);
    margin-top: 2rem;
    position: relative; z-index: 2;
    border: 1px solid rgba(255,255,255,.3);
}
.search-input-modern {
    border: 1px solid #dee2e6 !important;
    border-radius: 6px !important;
    padding: .375rem .75rem !important;
    font-size: .9rem !important;
    color: #212529 !important;
    background: #fff !important;
    transition: border-color .15s, box-shadow .15s !important;
    line-height: 1.5 !important;
}
.search-input-modern:focus {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 .25rem rgba(13,110,253,.25) !important;
    outline: 0 !important;
}
.search-input-modern::placeholder { color: #6c757d !important; opacity: 1 !important; }
.btn-search-main {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none; border-radius: 6px;
    color: #fff; font-weight: 600; padding: .375rem 1.5rem;
    font-size: .9rem; line-height: 1.5;
    transition: all .2s; white-space: nowrap;
    display: inline-flex; align-items: center;
}
.btn-search-main:hover { color: #fff; opacity: .9; }

/* Labels search */
.search-floating .form-label { font-size: .82rem; margin-bottom: .3rem; color: #495057; }
.search-floating .form-select { 
    border: 1px solid #dee2e6 !important; 
    border-radius: 6px !important; 
    font-size: .9rem !important;
    color: #212529 !important;
}
.search-floating .form-select:focus {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 .25rem rgba(13,110,253,.25) !important;
}
.search-floating .border-top { border-color: #dee2e6 !important; }
.search-floating .btn-outline-secondary {
    border-radius: 6px !important;
    font-size: .9rem !important;
}

/* ===== COURT CARDS V2 ===== */
.court-card-v2 {
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    transition: all .3s cubic-bezier(.4,0,.2,1);
    border: 1px solid #f0f0f0;
    height: 100%;
}
.court-card-v2:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,.14);
    border-color: #667eea;
}
.court-img-wrap {
    position: relative;
    height: 200px;
    overflow: hidden;
}
.court-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}
.court-card-v2:hover .court-img-wrap img { transform: scale(1.06); }
.court-badge-status {
    position: absolute; top: 12px; left: 12px;
    background: #28a745; color: #fff;
    font-size: .72rem; font-weight: 700;
    padding: 3px 10px; border-radius: 20px;
    box-shadow: 0 2px 8px rgba(40,167,69,.3);
}
.court-badge-price {
    position: absolute; top: 12px; right: 12px;
    background: rgba(0,0,0,.6); color: #fff;
    font-size: .78rem; font-weight: 700;
    padding: 3px 10px; border-radius: 20px;
    backdrop-filter: blur(4px);
}
.court-body { padding: 1.2rem 1.3rem 1.3rem; }
.court-title {
    font-size: 1rem; font-weight: 800; color: #111827;
    margin-bottom: .6rem; line-height: 1.3;
}
.court-meta {
    display: flex; flex-direction: column; gap: 3px;
    margin-bottom: .8rem;
}
.court-meta span {
    font-size: .8rem; color: #6b7280;
    display: flex; align-items: center; gap: 5px;
}
.court-meta i { color: #9ca3af; width: 14px; }
.court-tags { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 1rem; }
.court-tags span {
    background: #f3f4f6; color: #374151;
    font-size: .72rem; font-weight: 600;
    padding: 3px 8px; border-radius: 6px;
}
.court-footer {
    display: flex; gap: .5rem; align-items: center;
    padding-top: .8rem;
    border-top: 1px solid #f3f4f6;
}
.btn-book {
    flex: 1;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff; border: none; border-radius: 10px;
    padding: .55rem 1rem; font-weight: 700; font-size: .85rem;
    text-decoration: none; display: flex; align-items: center;
    justify-content: center; gap: 5px; transition: all .2s;
}
.btn-book:hover { color: #fff; opacity: .9; transform: translateY(-1px); }
.btn-call, .btn-map {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; text-decoration: none; transition: all .2s; flex-shrink: 0;
}
.btn-call { background: #d1fae5; color: #059669; }
.btn-call:hover { background: #059669; color: #fff; }
.btn-map { background: #dbeafe; color: #2563eb; }
.btn-map:hover { background: #2563eb; color: #fff; }

/* ===== FEATURES ===== */
.features-section { background: #f8fafc; padding: 4.5rem 0; }
.feature-card-v2 {
    background: #fff;
    border-radius: 18px;
    padding: 1.8rem;
    height: 100%;
    border: 1px solid #f0f0f0;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    transition: all .3s ease;
}
.feature-card-v2:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 35px rgba(0,0,0,.1);
    border-color: #e0e7ff;
}
.feature-icon-v2 {
    width: 56px; height: 56px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: 1.2rem;
}

/* ===== SECTION HEADERS ===== */
.section-eyebrow {
    font-size: .75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.5px; margin-bottom: .5rem;
}
.section-heading {
    font-size: 1.9rem; font-weight: 900; color: #111827; margin-bottom: .5rem;
}
.section-sub { color: #6b7280; font-size: .95rem; }

/* Wave divider */
.wave-hero { background: #1a1a2e; line-height: 0; }
.wave-hero svg { display: block; width: 100%; }
</style>

<!-- HERO SECTION -->
<div class="hero-section">
    <div class="container position-relative" style="z-index:2;">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-tag">
                    <i class="fas fa-shuttlecock"></i>
                    Nền tảng đặt sân #1 Hà Nội
                </div>
                <h1 class="hero-title">
                    Tìm sân &<br>
                    <span class="gradient-text">Đặt ngay</span> dễ dàng
                </h1>
                <p class="hero-desc">
                    Hơn 50+ sân cầu lông chất lượng, đặt sân 24/7 online, thanh toán an toàn qua MoMo & VNPay.
                </p>
                <div class="hero-stats">
                    <div class="hero-stat"><span class="n">50+</span><span class="l">Sân cầu lông</span></div>
                    <div class="hero-stat"><span class="n">5K+</span><span class="l">Lượt đặt</span></div>
                    <div class="hero-stat"><span class="n">4.8★</span><span class="l">Đánh giá</span></div>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#search" class="btn-hero-primary">
                        <i class="fas fa-search"></i> Tìm sân ngay
                    </a>
                    <a href="map.php" class="btn-hero-secondary">
                        <i class="fas fa-map"></i> Xem bản đồ
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <!-- Floating court preview -->
                <div style="position:relative;display:inline-block;">
                    <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:24px;padding:1.5rem;backdrop-filter:blur(10px);">
                        <div class="d-flex gap-3 mb-3">
                            <?php foreach (array_slice($courts, 0, 2) as $c): ?>
                            <div style="flex:1;background:rgba(255,255,255,.1);border-radius:14px;overflow:hidden;">
                                <img src="<?php echo escape($c['cover_image']); ?>" style="width:100%;height:100px;object-fit:cover;" alt="">
                                <div style="padding:.5rem .7rem;">
                                    <div style="color:#fff;font-weight:700;font-size:.8rem;"><?php echo escape($c['name']); ?></div>
                                    <div style="color:#a5b4fc;font-size:.72rem;"><?php echo number_format($c['price_per_hour']); ?>đ/h</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="background:rgba(102,126,234,.3);border-radius:12px;padding:.8rem;text-align:center;color:#fff;font-size:.85rem;font-weight:700;">
                            <i class="fas fa-calendar-check me-2 text-success"></i>
                            <?php echo count($courts); ?> sân đang trống hôm nay
                        </div>
                    </div>
                    <!-- Floating badges -->
                    <div style="position:absolute;top:-15px;right:-15px;background:linear-gradient(135deg,#28a745,#20c997);color:#fff;border-radius:12px;padding:.5rem .9rem;font-size:.78rem;font-weight:700;box-shadow:0 4px 15px rgba(40,167,69,.4);">
                        <i class="fas fa-bolt me-1"></i>Đặt ngay
                    </div>
                    <div style="position:absolute;bottom:-15px;left:-15px;background:#fff;border-radius:12px;padding:.5rem .9rem;font-size:.78rem;font-weight:700;color:#374151;box-shadow:0 4px 15px rgba(0,0,0,.15);">
                        <i class="fas fa-star text-warning me-1"></i>4.8 · 5K+ đánh giá
                    </div>
                </div>
            </div>
        </div>

        <!-- Search bar floating -->
        <div class="search-floating" id="search">
            <form id="searchForm">
                <!-- Row 1: Tìm kiếm, Khu vực, Mức giá -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark" style="font-size:.82rem;">
                            <i class="fas fa-search text-primary me-1"></i>Tìm kiếm
                        </label>
                        <input type="search" name="q" class="form-control search-input-modern" id="searchInput"
                               value="<?php echo escape($filters['q']); ?>"
                               placeholder="Tên sân, phường/xã...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark" style="font-size:.82rem;">
                            <i class="fas fa-map-marker-alt text-success me-1"></i>Khu vực
                        </label>
                        <input list="locationList" type="text" name="location" class="form-control search-input-modern" id="locationInput"
                               value="<?php echo escape($filters['location']); ?>"
                               placeholder="Chọn phường/xã...">
                        <datalist id="locationList">
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?php echo escape($loc); ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark" style="font-size:.82rem;">
                            <i class="fas fa-money-bill text-warning me-1"></i>Mức giá
                        </label>
                        <select name="price" class="form-select search-input-modern" id="priceSelect">
                            <option value="">Tất cả mức giá</option>
                            <option value="low" <?php echo $filters['price']==='low'?'selected':''; ?>>Dưới 100k/giờ</option>
                            <option value="mid" <?php echo $filters['price']==='mid'?'selected':''; ?>>100k – 150k/giờ</option>
                            <option value="high" <?php echo $filters['price']==='high'?'selected':''; ?>>Trên 150k/giờ</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Giá min, Giá max, Sắp xếp, Reset, Buttons -->
                <div class="row g-3 align-items-end border-top pt-3">
                    <div class="col-md-2">
                        <label class="form-label text-muted" style="font-size:.82rem;">Giá tối thiểu</label>
                        <input type="number" name="min_price" min="0" class="form-control search-input-modern" id="minPriceInput"
                               value="<?php echo escape($filters['min_price']); ?>"
                               placeholder="VD: 80000">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted" style="font-size:.82rem;">Giá tối đa</label>
                        <input type="number" name="max_price" min="0" class="form-control search-input-modern" id="maxPriceInput"
                               value="<?php echo escape($filters['max_price']); ?>"
                               placeholder="VD: 200000">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted" style="font-size:.82rem;">Sắp xếp theo</label>
                        <select name="sort" class="form-select search-input-modern" id="sortSelect">
                            <option value="">Mặc định</option>
                            <option value="price_asc" <?php echo $filters['sort']==='price_asc'?'selected':''; ?>>Giá thấp → cao</option>
                            <option value="price_desc" <?php echo $filters['sort']==='price_desc'?'selected':''; ?>>Giá cao → thấp</option>
                            <option value="newest" <?php echo $filters['sort']==='newest'?'selected':''; ?>>Mới nhất</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100" onclick="window.homepageSearch && window.homepageSearch.resetSearch()">
                            <i class="fas fa-times me-1"></i>Xóa bộ lọc
                        </button>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" id="searchBtn" class="btn-search-main flex-grow-1">
                            <i class="fas fa-search me-2"></i>Tìm sân
                        </button>
                        <a href="map.php" class="btn btn-outline-primary d-flex align-items-center gap-1 px-3" style="white-space:nowrap;">
                            <i class="fas fa-map me-1"></i>Xem bản đồ
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="wave-hero">
    <svg viewBox="0 0 1440 50" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,25 C360,50 1080,0 1440,25 L1440,50 L0,50 Z" fill="#f8fafc"/>
    </svg>
</div>

<!-- FEATURES SECTION -->
<section class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow text-primary">Tại sao chọn chúng tôi</div>
            <h2 class="section-heading">Trải nghiệm đặt sân tuyệt vời nhất</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-v2">
                    <div class="feature-icon-v2" style="background:#eff6ff;color:#3b82f6;">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Đặt nhanh</h5>
                    <p class="text-muted small mb-2">Chỉ 3 bước để hoàn tất đặt sân trong vòng 2 phút.</p>
                    <small style="color:#3b82f6;font-weight:700;">Tìm → Chọn giờ → Thanh toán</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-v2">
                    <div class="feature-icon-v2" style="background:#f0fdf4;color:#22c55e;">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">50+ Sân</h5>
                    <p class="text-muted small mb-2">Sân chất lượng cao tại 8 quận/huyện Hà Nội.</p>
                    <small style="color:#22c55e;font-weight:700;">Sân mới, hiện đại, có mái che</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-v2">
                    <div class="feature-icon-v2" style="background:#ecfdf5;color:#10b981;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Lịch rõ ràng</h5>
                    <p class="text-muted small mb-2">Xem lịch trống 24/7, tránh chồng lịch 100%.</p>
                    <small style="color:#10b981;font-weight:700;">Cập nhật thời gian thực</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card-v2">
                    <div class="feature-icon-v2" style="background:#fffbeb;color:#f59e0b;">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Thanh toán linh hoạt</h5>
                    <p class="text-muted small mb-2">MoMo, VNPay hoặc tiền mặt tại sân.</p>
                    <small style="color:#f59e0b;font-weight:700;">An toàn, bảo mật 100%</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- COURT RESULTS -->
<section style="background:#fff;padding:3.5rem 0;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <div class="section-eyebrow text-primary">Danh sách sân</div>
                <h2 class="section-heading mb-0">Tìm sân cầu lông</h2>
            </div>
            <div class="d-flex gap-2">
                <button id="resetBtn" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fas fa-times me-1"></i>Xóa lọc
                </button>
                <a href="map.php" class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="fas fa-map me-1"></i>Bản đồ
                </a>
            </div>
        </div>

        <div id="courtsResults" class="row g-4">
            <?php if (empty($courts)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted opacity-50 mb-3 d-block"></i>
                        <h5 class="text-muted">Không tìm thấy sân phù hợp</h5>
                        <a href="index.php" class="btn btn-primary btn-sm mt-2">Xem tất cả sân</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($courts as $court):
                $phone = $court['phone'] ?? '0968073500';
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="court-card-v2">
                    <div class="court-img-wrap">
                        <img src="<?php echo escape($court['cover_image']); ?>" alt="<?php echo escape($court['name']); ?>">
                        <span class="court-badge-status">Còn trống</span>
                        <span class="court-badge-price"><?php echo number_format($court['price_per_hour']); ?>đ/h</span>
                    </div>
                    <div class="court-body">
                        <h5 class="court-title"><?php echo escape($court['name']); ?></h5>
                        <div class="court-meta">
                            <span><i class="fas fa-map-marker-alt"></i><?php echo escape($court['location']); ?></span>
                            <span><i class="fas fa-clock"></i>6:00 – 22:00</span>
                        </div>
                        <div class="court-tags">
                            <span>Mái che</span><span>Sân gỗ</span><span>Điều hòa</span>
                        </div>
                        <div class="court-footer">
                            <a href="booking-online.php?court_id=<?php echo $court['id']; ?>" class="btn-book">
                                <i class="fas fa-calendar-check me-1"></i>Đặt sân
                            </a>
                            <?php
                            $phone = $court['phone'] ?? '0968073500';
                            $phoneDisplay = '0968.073.500';
                            ?>
                            <a href="tel:<?php echo $phone; ?>" class="btn-call"
                               title="Gọi ngay"
                               onclick="return confirmCallIndex('<?php echo escape($court['name']); ?>', '<?php echo $phone; ?>', '<?php echo $phoneDisplay; ?>')">
                                <i class="fas fa-phone"></i>
                            </a>
                            <a href="map.php?court_id=<?php echo $court['id']; ?>&name=<?php echo urlencode($court['name']); ?>&lat=<?php echo $court['latitude'] ?? '21.0285'; ?>&lng=<?php echo $court['longitude'] ?? '105.8542'; ?>"
                               class="btn-map" title="Chỉ đường">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Khu vực phủ sóng -->
<section style="background:#f8fafc;padding:3.5rem 0;">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-eyebrow text-primary">Khu vực</div>
            <h2 class="section-heading">Khu vực phủ sóng</h2>
            <p class="section-sub">Chọn khu vực để tìm sân gần bạn nhất</p>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
            <?php foreach ($locations as $location): ?>
                <a href="index.php?location=<?php echo urlencode($location); ?>"
                   class="btn btn-outline-primary btn-sm rounded-pill <?php echo $filters['location']===$location?'active':''; ?>">
                    <i class="fas fa-map-marker-alt me-1"></i><?php echo escape($location); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="text-center">
            <a href="map.php" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-map me-2"></i>Xem tất cả trên bản đồ
            </a>
        </div>
    </div>
</section>

<!-- Map preview section -->
<section style="background:#fff;padding:3.5rem 0;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="section-eyebrow text-primary">Bản đồ tương tác</div>
                <h2 class="section-heading">Khám phá sân trên bản đồ</h2>
                <p class="text-muted mb-4">Tìm kiếm sân gần bạn, xem vị trí chính xác và chỉ đường tích hợp Google Maps.</p>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#3b82f6;"><i class="fas fa-map-marker-alt"></i></div>
                            <div><div class="fw-bold" style="font-size:.85rem;">Định vị GPS</div><small class="text-muted">Tìm sân gần nhất</small></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#22c55e;"><i class="fas fa-route"></i></div>
                            <div><div class="fw-bold" style="font-size:.85rem;">Chỉ đường</div><small class="text-muted">Google Maps</small></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#f59e0b;"><i class="fas fa-filter"></i></div>
                            <div><div class="fw-bold" style="font-size:.85rem;">Lọc thông minh</div><small class="text-muted">Theo khoảng cách & giá</small></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#8b5cf6;"><i class="fas fa-sync-alt"></i></div>
                            <div><div class="fw-bold" style="font-size:.85rem;">Thời gian thực</div><small class="text-muted">Trạng thái sân</small></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="map.php" class="btn-hero-primary">
                        <i class="fas fa-map"></i> Mở bản đồ
                    </a>
                    <a href="map.php" class="btn btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-search-location me-1"></i>Tìm sân gần tôi
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);border-radius:20px;padding:1.5rem;position:relative;">
                    <div style="background:rgba(255,255,255,.05);border-radius:14px;overflow:hidden;height:260px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.1);">
                        <div class="text-center">
                            <div style="width:70px;height:70px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.8rem;color:#fff;">
                                <i class="fas fa-map"></i>
                            </div>
                            <div style="color:#fff;font-weight:700;margin-bottom:.5rem;">Bản đồ tương tác Hà Nội</div>
                            <div style="color:rgba(255,255,255,.5);font-size:.8rem;">20+ sân · 8 quận/huyện</div>
                        </div>
                    </div>
                    <div style="position:absolute;top:-12px;right:20px;background:linear-gradient(135deg,#28a745,#20c997);color:#fff;border-radius:20px;padding:4px 12px;font-size:.75rem;font-weight:700;">
                        <i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Live
                    </div>
                    <div style="position:absolute;bottom:-12px;left:20px;background:#fff;border-radius:20px;padding:4px 12px;font-size:.75rem;font-weight:700;color:#374151;box-shadow:0 4px 12px rgba(0,0,0,.15);">
                        <i class="fas fa-star text-warning me-1"></i>50+ sân
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Modal gọi điện -->
<div class="modal fade" id="callModalIndex" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#0d6efd,#0dcaf0);padding:1.4rem 1.5rem;text-align:center;color:#fff;">
                <div style="width:60px;height:60px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto .8rem;">
                    <i class="fas fa-phone fa-xl"></i>
                </div>
                <h5 class="fw-bold mb-1">Gọi cho sân</h5>
                <div id="callCourtNameIndex" style="opacity:.85;font-size:.9rem;"></div>
            </div>
            <div style="padding:1.5rem;text-align:center;">
                <div style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">Số điện thoại liên hệ:</div>
                <div id="callPhoneDisplayIndex" style="font-size:1.8rem;font-weight:900;color:#0d6efd;letter-spacing:2px;margin-bottom:.5rem;"></div>
                <div style="font-size:.78rem;color:#9ca3af;margin-bottom:1.5rem;"><i class="fas fa-clock me-1"></i>Hỗ trợ 6:00 – 22:00</div>
                <div class="d-grid gap-2">
                    <a id="callPhoneLinkIndex" href="#" class="btn btn-primary btn-lg fw-bold"
                       style="border-radius:14px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);border:none;"
                       onclick="bootstrap.Modal.getInstance(document.getElementById('callModalIndex')).hide()">
                        <i class="fas fa-phone me-2"></i>Gọi ngay
                    </a>
                    <button class="btn btn-outline-secondary" style="border-radius:14px;" data-bs-dismiss="modal">Huỷ</button>
                </div>
            </div>
            <div style="padding:.7rem;background:#f9fafb;text-align:center;font-size:.75rem;color:#9ca3af;border-top:1px solid #f0f0f0;">
                <i class="fas fa-shield-alt me-1 text-success"></i>Cuộc gọi trực tiếp — không qua trung gian
            </div>
        </div>
    </div>
</div>

<script>
function confirmCallIndex(courtName, phone, phoneDisplay) {
    document.getElementById('callCourtNameIndex').textContent   = courtName;
    document.getElementById('callPhoneDisplayIndex').textContent = phoneDisplay;
    document.getElementById('callPhoneLinkIndex').href           = 'tel:' + phone;
    new bootstrap.Modal(document.getElementById('callModalIndex')).show();
    return false;
}
</script>

<script src="assets/js/homepage-search.js"></script>