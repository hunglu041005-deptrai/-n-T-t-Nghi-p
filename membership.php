<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$plans = [
    ['id'=>1,'label'=>'COMBO CHIỀU 14H–17H','sub'=>'10 VÉ TẶNG 1 VÉ','price'=>720000,'months'=>3,'free'=>11,'time'=>'14H–17H','price_per'=>80000,'popular'=>false],
    ['id'=>2,'label'=>'COMBO CHIỀU 14H–17H','sub'=>'20 VÉ TẶNG 2 VÉ','price'=>1440000,'months'=>6,'free'=>22,'time'=>'14H–17H','price_per'=>80000,'popular'=>true],
    ['id'=>3,'label'=>'COMBO TỐI 20H–22H (T7,CN)','sub'=>'20 VÉ TẶNG 2 VÉ','price'=>1440000,'months'=>9,'free'=>22,'time'=>'20H–22H','price_per'=>80000,'popular'=>false],
    ['id'=>4,'label'=>'COMBO TỐI 20H–22H (T7,CN)','sub'=>'30 VÉ TẶNG 3 VÉ','price'=>2160000,'months'=>12,'free'=>33,'time'=>'20H–22H','price_per'=>80000,'popular'=>false],
    ['id'=>5,'label'=>'COMBO CHIỀU 15H–18H','sub'=>'10 VÉ TẶNG 1 VÉ','price'=>720000,'months'=>3,'free'=>11,'time'=>'15H–18H','price_per'=>80000,'popular'=>false],
    ['id'=>6,'label'=>'COMBO CHIỀU 15H–18H','sub'=>'20 VÉ TẶNG 2 VÉ','price'=>1440000,'months'=>6,'free'=>22,'time'=>'15H–18H','price_per'=>80000,'popular'=>false],
];
?>

<style>
/* ===== MEMBERSHIP PAGE ===== */
.membership-page {
    background: #f0f4f8;
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* Hero Banner */
.membership-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%);
    padding: 4rem 0 6rem;
    position: relative;
    overflow: hidden;
}

.membership-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(40,167,69,.15) 0%, transparent 70%);
    border-radius: 50%;
}

.membership-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(102,126,234,.1) 0%, transparent 70%);
    border-radius: 50%;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(40,167,69,.2);
    border: 1px solid rgba(40,167,69,.4);
    color: #4ade80;
    padding: 6px 16px;
    border-radius: 50px;
    font-size: .82rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.hero-title {
    font-size: 2.8rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.hero-title span {
    background: linear-gradient(135deg, #4ade80, #22d3ee);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    color: rgba(255,255,255,.65);
    font-size: 1.05rem;
    margin-bottom: 2rem;
    max-width: 500px;
}

.hero-stats {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.hero-stat {
    text-align: center;
}

.hero-stat .num {
    font-size: 1.8rem;
    font-weight: 800;
    color: #4ade80;
    display: block;
}

.hero-stat .lbl {
    font-size: .78rem;
    color: rgba(255,255,255,.5);
}

/* Wave divider */
.wave-divider {
    margin-top: -2px;
    line-height: 0;
}

.wave-divider svg {
    display: block;
    width: 100%;
}

/* Plans section */
.plans-section {
    padding: 3rem 0;
}

.section-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.section-header h2 {
    font-size: 1.8rem;
    font-weight: 800;
    color: #1a1a2e;
    margin-bottom: .5rem;
}

.section-header p {
    color: #6b7280;
    font-size: .95rem;
}

/* Plan card */
.plan-card {
    background: #fff;
    border-radius: 20px;
    padding: 1.8rem;
    margin-bottom: 1.2rem;
    border: 2px solid #e5e7eb;
    transition: all .3s cubic-bezier(.4,0,.2,1);
    position: relative;
    overflow: hidden;
}

.plan-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #28a745, #20c997);
    opacity: 0;
    transition: opacity .3s;
}

.plan-card:hover {
    border-color: #28a745;
    box-shadow: 0 12px 40px rgba(40,167,69,.12);
    transform: translateY(-3px);
}

.plan-card:hover::before {
    opacity: 1;
}

.plan-card.popular {
    border-color: #28a745;
    box-shadow: 0 8px 30px rgba(40,167,69,.15);
}

.plan-card.popular::before {
    opacity: 1;
}

.popular-badge {
    position: absolute;
    top: 1.2rem;
    right: 1.2rem;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    color: #fff;
    font-size: .72rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.plan-num-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: #fff;
    border-radius: 10px;
    padding: 5px 14px;
    font-size: .8rem;
    font-weight: 700;
    margin-bottom: .8rem;
}

.plan-price-tag {
    font-size: .72rem;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-bottom: .3rem;
}

.plan-name {
    font-size: 1rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: .6rem;
    line-height: 1.4;
}

.plan-date-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .8rem;
    color: #9ca3af;
    margin-bottom: .8rem;
}

.plan-meta-row {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1.2rem;
}

.meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: .8rem;
    font-weight: 600;
    color: #374151;
}

.meta-chip .chip-val {
    color: #f59e0b;
    font-weight: 800;
}

.meta-chip i {
    color: #f59e0b;
}

.plan-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

.plan-price-big {
    font-size: 1.5rem;
    font-weight: 800;
    color: #111827;
}

.plan-price-big span {
    font-size: .85rem;
    font-weight: 500;
    color: #9ca3af;
}

.btn-register {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: .6rem 1.6rem;
    font-weight: 700;
    font-size: .9rem;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 15px rgba(40,167,69,.3);
}

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40,167,69,.4);
    color: #fff;
}

/* Benefits section */
.benefits-section {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: .8rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.benefit-item:last-child { border-bottom: none; }

.benefit-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.benefit-icon.green { background: rgba(40,167,69,.1); color: #28a745; }
.benefit-icon.blue  { background: rgba(59,130,246,.1); color: #3b82f6; }
.benefit-icon.orange{ background: rgba(245,158,11,.1); color: #f59e0b; }
.benefit-icon.purple{ background: rgba(139,92,246,.1); color: #8b5cf6; }

.benefit-text h6 { font-weight: 700; font-size: .9rem; margin-bottom: .2rem; color: #111827; }
.benefit-text p  { font-size: .8rem; color: #6b7280; margin: 0; }

/* CTA section */
.cta-section {
    background: linear-gradient(135deg, #1a1a2e, #0f3460);
    border-radius: 20px;
    padding: 2.5rem;
    text-align: center;
    color: #fff;
    margin-bottom: 1.5rem;
}

.cta-section h3 { font-weight: 800; margin-bottom: .5rem; }
.cta-section p  { color: rgba(255,255,255,.65); margin-bottom: 1.5rem; }

.btn-cta {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: .85rem 2.5rem;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 8px 25px rgba(40,167,69,.4);
}

.btn-cta:hover { transform: translateY(-2px); color: #fff; }

/* Animations */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.plan-card { animation: fadeUp .4s ease both; }
.plan-card:nth-child(1) { animation-delay: .05s; }
.plan-card:nth-child(2) { animation-delay: .1s; }
.plan-card:nth-child(3) { animation-delay: .15s; }
.plan-card:nth-child(4) { animation-delay: .2s; }
.plan-card:nth-child(5) { animation-delay: .25s; }
.plan-card:nth-child(6) { animation-delay: .3s; }
</style>

<div class="membership-page">

    <!-- Hero -->
    <div class="membership-hero">
        <div class="container position-relative" style="z-index:2;">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-badge">
                        <i class="fas fa-id-card"></i> Gói hội viên ưu đãi
                    </div>
                    <h1 class="hero-title">
                        Chơi thả ga với<br>
                        <span>gói hội viên</span>
                    </h1>
                    <p class="hero-subtitle">
                        Mua combo vé, nhận vé miễn phí — tiết kiệm đến 10% so với giá lẻ. Thời hạn linh hoạt từ 3 đến 12 tháng.
                    </p>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <span class="num">6+</span>
                            <span class="lbl">Gói hội viên</span>
                        </div>
                        <div class="hero-stat">
                            <span class="num">10%</span>
                            <span class="lbl">Tiết kiệm tối đa</span>
                        </div>
                        <div class="hero-stat">
                            <span class="num">80K</span>
                            <span class="lbl">Giá/vé ưu đãi</span>
                        </div>
                        <div class="hero-stat">
                            <span class="num">12</span>
                            <span class="lbl">Tháng tối đa</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                    <div style="width:280px;height:280px;background:rgba(40,167,69,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid rgba(40,167,69,.2);">
                        <div style="width:200px;height:200px;background:rgba(40,167,69,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-id-card" style="font-size:5rem;color:rgba(74,222,128,.6);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave -->
    <div class="wave-divider" style="background:#1a1a2e;">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f0f4f8"/>
        </svg>
    </div>

    <!-- Main content -->
    <div class="plans-section">
        <div class="container">
            <div class="row">

                <!-- Plans list -->
                <div class="col-lg-8">
                    <div class="section-header text-start mb-4">
                        <h2 style="font-size:1.5rem;">Danh sách gói hội viên</h2>
                        <p>Chọn gói phù hợp với lịch chơi của bạn</p>
                    </div>

                    <?php foreach ($plans as $p): ?>
                    <div class="plan-card <?php echo $p['popular'] ? 'popular' : ''; ?>">
                        <?php if ($p['popular']): ?>
                            <div class="popular-badge">⭐ Phổ biến nhất</div>
                        <?php endif; ?>

                        <div class="plan-num-badge">
                            <?php echo $p['id']; ?> Thẻ hội viên
                        </div>

                        <div class="plan-price-tag">GIÁ <?php echo number_format($p['price_per']/1000); ?>0K/VÉ</div>
                        <div class="plan-name"><?php echo $p['label']; ?> : <?php echo $p['sub']; ?></div>

                        <div class="plan-date-row">
                            <i class="fas fa-calendar-alt text-success"></i>
                            Mở bán: 01/06/2026 – 31/12/2026
                        </div>

                        <div class="plan-meta-row">
                            <div class="meta-chip">
                                <i class="fas fa-clock"></i>
                                Thời hạn <span class="chip-val"><?php echo $p['months']; ?> Tháng</span>
                            </div>
                            <div class="meta-chip">
                                <i class="fas fa-gift"></i>
                                Miễn phí <span class="chip-val"><?php echo $p['free']; ?> vé</span>
                            </div>
                            <div class="meta-chip">
                                <i class="fas fa-shuttlecock"></i>
                                Cầu lông
                            </div>
                        </div>

                        <div class="plan-bottom">
                            <div class="plan-price-big">
                                <?php echo number_format($p['price']); ?> đ
                                <span>/ <?php echo $p['months']; ?> tháng</span>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <a href="#" class="btn-register" onclick="registerPlan(<?php echo $p['id']; ?>); return false;">
                                    Đăng ký <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <a href="login.php?redirect=membership.php" class="btn-register">
                                    Đăng ký <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">

                    <!-- Benefits -->
                    <div class="benefits-section">
                        <h6 class="fw-bold mb-3" style="color:#111827;">
                            <i class="fas fa-star text-warning me-2"></i>Quyền lợi hội viên
                        </h6>
                        <div class="benefit-item">
                            <div class="benefit-icon green"><i class="fas fa-ticket-alt"></i></div>
                            <div class="benefit-text">
                                <h6>Vé ưu đãi 80K</h6>
                                <p>Giá cố định, không tăng theo giờ cao điểm</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <div class="benefit-icon blue"><i class="fas fa-gift"></i></div>
                            <div class="benefit-text">
                                <h6>Tặng vé miễn phí</h6>
                                <p>Mua 10 tặng 1, mua 20 tặng 2, mua 30 tặng 3</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <div class="benefit-icon orange"><i class="fas fa-clock"></i></div>
                            <div class="benefit-text">
                                <h6>Thời hạn linh hoạt</h6>
                                <p>Sử dụng trong 3, 6, 9 hoặc 12 tháng</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <div class="benefit-icon purple"><i class="fas fa-headset"></i></div>
                            <div class="benefit-text">
                                <h6>Hỗ trợ ưu tiên</h6>
                                <p>Đặt sân nhanh, không cần chờ đợi</p>
                            </div>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="cta-section">
                        <h3>Cần tư vấn?</h3>
                        <p>Liên hệ ngay để được hỗ trợ chọn gói phù hợp nhất</p>
                        <a href="tel:0123456789" class="btn-cta">
                            <i class="fas fa-phone"></i> 0123.456.789
                        </a>
                    </div>

                    <!-- FAQ -->
                    <div class="benefits-section">
                        <h6 class="fw-bold mb-3" style="color:#111827;">
                            <i class="fas fa-question-circle text-primary me-2"></i>Câu hỏi thường gặp
                        </h6>
                        <div class="accordion accordion-flush" id="faqAccordion">
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed px-0 py-3" style="font-size:.85rem;font-weight:600;" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Vé có hết hạn không?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body px-0 pt-0 pb-3" style="font-size:.82rem;color:#6b7280;">
                                        Vé có thời hạn theo gói bạn chọn (3, 6, 9 hoặc 12 tháng kể từ ngày kích hoạt).
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed px-0 py-3" style="font-size:.85rem;font-weight:600;" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Có thể chuyển nhượng vé không?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body px-0 pt-0 pb-3" style="font-size:.82rem;color:#6b7280;">
                                        Vé hội viên được sử dụng cho tài khoản đăng ký, không chuyển nhượng.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed px-0 py-3" style="font-size:.85rem;font-weight:600;" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Thanh toán bằng gì?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body px-0 pt-0 pb-3" style="font-size:.82rem;color:#6b7280;">
                                        Hỗ trợ MoMo, VNPay, chuyển khoản ngân hàng và tiền mặt tại sân.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== PAYMENT MODAL ===== -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">

            <!-- Header -->
            <div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:1.5rem 2rem;color:#fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fas fa-credit-card me-2"></i>Thanh toán gói hội viên</h5>
                        <small style="color:rgba(255,255,255,.6);">Hoàn tất đăng ký để nhận thẻ hội viên</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- Left: Order summary -->
                    <div class="col-md-5" style="background:#f8fafc;padding:1.8rem;border-right:1px solid #e5e7eb;">
                        <h6 class="fw-bold mb-3" style="color:#374151;">Thông tin gói</h6>

                        <div id="pm-plan-info" style="background:#fff;border-radius:14px;padding:1.2rem;border:1px solid #e5e7eb;margin-bottom:1rem;">
                            <div id="pm-plan-badge" class="mb-2" style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#28a745,#20c997);color:#fff;border-radius:8px;padding:4px 12px;font-size:.8rem;font-weight:700;"></div>
                            <div id="pm-plan-label" style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;margin-bottom:.2rem;"></div>
                            <div id="pm-plan-name" style="font-weight:800;font-size:.95rem;color:#111;margin-bottom:.8rem;"></div>
                            <div style="display:flex;gap:.8rem;flex-wrap:wrap;font-size:.8rem;">
                                <span id="pm-months" style="background:#fef3c7;color:#d97706;border-radius:6px;padding:3px 8px;font-weight:700;"></span>
                                <span id="pm-free" style="background:#d1fae5;color:#059669;border-radius:6px;padding:3px 8px;font-weight:700;"></span>
                            </div>
                        </div>

                        <div style="background:#fff;border-radius:14px;padding:1.2rem;border:1px solid #e5e7eb;">
                            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                                <span style="color:#6b7280;">Giá gói</span>
                                <span id="pm-price-sub" class="fw-bold"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                                <span style="color:#6b7280;">Vé tặng thêm</span>
                                <span id="pm-free-val" style="color:#28a745;font-weight:700;"></span>
                            </div>
                            <hr style="margin:.8rem 0;">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Tổng thanh toán</span>
                                <span id="pm-total" style="font-size:1.2rem;font-weight:800;color:#111;"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Payment methods -->
                    <div class="col-md-7" style="padding:1.8rem;">
                        <h6 class="fw-bold mb-3" style="color:#374151;">Phương thức thanh toán</h6>

                        <div class="pm-methods">
                            <!-- Cash -->
                            <label class="pm-method-card selected" data-method="cash">
                                <input type="radio" name="pm_method" value="cash" checked style="display:none;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:44px;height:44px;background:#d1fae5;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-money-bill-wave" style="color:#28a745;font-size:1.1rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="font-size:.9rem;">Tiền mặt tại sân</div>
                                        <div style="font-size:.78rem;color:#9ca3af;">Thanh toán khi đến sân lần đầu</div>
                                    </div>
                                    <div class="pm-check"><i class="fas fa-check-circle text-success"></i></div>
                                </div>
                            </label>

                            <!-- MoMo -->
                            <label class="pm-method-card" data-method="momo">
                                <input type="radio" name="pm_method" value="momo" style="display:none;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:44px;height:44px;background:#fce7f3;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-wallet" style="color:#ec4899;font-size:1.1rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="font-size:.9rem;">Ví MoMo</div>
                                        <div style="font-size:.78rem;color:#9ca3af;">Thanh toán qua ví điện tử MoMo</div>
                                    </div>
                                    <div class="pm-check" style="opacity:0;"><i class="fas fa-check-circle text-success"></i></div>
                                </div>
                            </label>

                            <!-- VNPay -->
                            <label class="pm-method-card" data-method="vnpay">
                                <input type="radio" name="pm_method" value="vnpay" style="display:none;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:44px;height:44px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-university" style="color:#3b82f6;font-size:1.1rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="font-size:.9rem;">VNPay</div>
                                        <div style="font-size:.78rem;color:#9ca3af;">Thanh toán qua ngân hàng</div>
                                    </div>
                                    <div class="pm-check" style="opacity:0;"><i class="fas fa-check-circle text-success"></i></div>
                                </div>
                            </label>
                        </div>

                        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:.8rem 1rem;margin-top:1rem;font-size:.8rem;color:#166534;">
                            <i class="fas fa-shield-alt me-2"></i>
                            Giao dịch được bảo mật SSL 256-bit
                        </div>

                        <button id="btn-confirm-payment" class="btn w-100 mt-3 py-3 fw-bold" style="background:linear-gradient(135deg,#28a745,#20c997);color:#fff;border:none;border-radius:14px;font-size:1rem;box-shadow:0 8px 25px rgba(40,167,69,.3);transition:all .2s;">
                            <i class="fas fa-lock me-2"></i>Xác nhận đăng ký
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MEMBER CARD MODAL ===== -->
<div class="modal fade" id="memberCardModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">

            <div style="background:linear-gradient(135deg,#28a745,#20c997);padding:1.5rem 2rem;color:#fff;text-align:center;">
                <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                <h5 class="fw-bold mb-1">Đăng ký thành công!</h5>
                <small style="opacity:.8;">Thẻ hội viên của bạn đã được kích hoạt</small>
            </div>

            <div class="modal-body p-0">
                <!-- Printable member card -->
                <div id="member-card-print" style="padding:1.5rem;">

                    <!-- Card design -->
                    <div id="member-card" style="background:linear-gradient(135deg,#1a1a2e 0%,#0f3460 100%);border-radius:18px;padding:1.8rem;color:#fff;position:relative;overflow:hidden;margin-bottom:1rem;">
                        <!-- Background decoration -->
                        <div style="position:absolute;top:-30px;right:-30px;width:150px;height:150px;background:rgba(40,167,69,.15);border-radius:50%;"></div>
                        <div style="position:absolute;bottom:-40px;left:-20px;width:120px;height:120px;background:rgba(102,126,234,.1);border-radius:50%;"></div>

                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3" style="position:relative;z-index:1;">
                            <div>
                                <div style="font-size:.7rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:1px;">BadmintonPro</div>
                                <div style="font-size:1.1rem;font-weight:800;color:#4ade80;">THẺ HỘI VIÊN</div>
                            </div>
                            <div style="background:rgba(74,222,128,.2);border:1px solid rgba(74,222,128,.4);border-radius:8px;padding:4px 10px;font-size:.72rem;font-weight:700;color:#4ade80;" id="mc-status">ACTIVE</div>
                        </div>

                        <!-- Member code big -->
                        <div style="text-align:center;margin:1rem 0;position:relative;z-index:1;">
                            <div style="font-size:.7rem;color:rgba(255,255,255,.5);margin-bottom:.3rem;">MÃ THẺ HỘI VIÊN</div>
                            <div id="mc-code" style="font-size:1.8rem;font-weight:900;letter-spacing:4px;color:#fff;font-family:monospace;"></div>
                        </div>

                        <!-- QR Code -->
                        <div style="text-align:center;margin:1rem 0;position:relative;z-index:1;">
                            <div id="mc-qr" style="display:inline-block;background:#fff;padding:10px;border-radius:12px;"></div>
                            <div style="font-size:.7rem;color:rgba(255,255,255,.5);margin-top:.5rem;">Quét mã để xác nhận hội viên</div>
                        </div>

                        <!-- Info grid -->
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;position:relative;z-index:1;">
                            <div style="background:rgba(255,255,255,.08);border-radius:10px;padding:.7rem;">
                                <div style="font-size:.65rem;color:rgba(255,255,255,.5);text-transform:uppercase;">Họ tên</div>
                                <div id="mc-name" style="font-weight:700;font-size:.85rem;"></div>
                            </div>
                            <div style="background:rgba(255,255,255,.08);border-radius:10px;padding:.7rem;">
                                <div style="font-size:.65rem;color:rgba(255,255,255,.5);text-transform:uppercase;">Gói</div>
                                <div id="mc-plan" style="font-weight:700;font-size:.85rem;color:#4ade80;"></div>
                            </div>
                            <div style="background:rgba(255,255,255,.08);border-radius:10px;padding:.7rem;">
                                <div style="font-size:.65rem;color:rgba(255,255,255,.5);text-transform:uppercase;">Hiệu lực từ</div>
                                <div id="mc-start" style="font-weight:700;font-size:.85rem;"></div>
                            </div>
                            <div style="background:rgba(255,255,255,.08);border-radius:10px;padding:.7rem;">
                                <div style="font-size:.65rem;color:rgba(255,255,255,.5);text-transform:uppercase;">Hết hạn</div>
                                <div id="mc-end" style="font-weight:700;font-size:.85rem;color:#fbbf24;"></div>
                            </div>
                        </div>

                        <!-- Tickets info -->
                        <div style="margin-top:.8rem;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.2);border-radius:10px;padding:.7rem;text-align:center;position:relative;z-index:1;">
                            <span id="mc-tickets" style="font-weight:700;color:#4ade80;font-size:.9rem;"></span>
                        </div>
                    </div>

                    <!-- Print note -->
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:1rem;font-size:.82rem;color:#166534;text-align:center;">
                        <i class="fas fa-info-circle me-2"></i>
                        Xuất trình mã thẻ hoặc QR code cho nhân viên khi đến sân
                    </div>
                </div>
            </div>

            <div style="padding:1rem 1.5rem;border-top:1px solid #f3f4f6;display:flex;gap:.8rem;">
                <button onclick="printMemberCard()" class="btn flex-grow-1 py-2 fw-bold" style="background:linear-gradient(135deg,#1a1a2e,#0f3460);color:#fff;border:none;border-radius:12px;">
                    <i class="fas fa-print me-2"></i>In thẻ hội viên
                </button>
                <button class="btn py-2 fw-bold" style="background:#f3f4f6;color:#374151;border:none;border-radius:12px;padding:0 1.5rem;" data-bs-dismiss="modal">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pm-method-card {
    display: block;
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    padding: 1rem 1.2rem;
    margin-bottom: .8rem;
    cursor: pointer;
    transition: all .2s;
}
.pm-method-card:hover { border-color: #28a745; }
.pm-method-card.selected {
    border-color: #28a745;
    background: #f0fdf4;
}
.pm-method-card.selected .pm-check { opacity: 1 !important; }

@media print {
    body * { visibility: hidden; }
    #member-card-print, #member-card-print * { visibility: visible; }
    #member-card-print { position: fixed; top: 0; left: 0; width: 100%; }
    .modal-footer, button { display: none !important; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
const plansData = {
    1: {badge:'1 Thẻ hội viên', label:'GIÁ 80K/VÉ', name:'COMBO CHIỀU 14H–17H : 10 VÉ TẶNG 1 VÉ', price:720000, months:3, free:11},
    2: {badge:'2 Thẻ hội viên', label:'GIÁ 80K/VÉ', name:'COMBO CHIỀU 14H–17H : 20 VÉ TẶNG 2 VÉ', price:1440000, months:6, free:22},
    3: {badge:'3 Thẻ hội viên', label:'GIÁ 80K/VÉ', name:'COMBO TỐI 20H–22H : 20 VÉ TẶNG 2 VÉ', price:1440000, months:9, free:22},
    4: {badge:'4 Thẻ hội viên', label:'GIÁ 80K/VÉ', name:'COMBO TỐI 20H–22H : 30 VÉ TẶNG 3 VÉ', price:2160000, months:12, free:33},
    5: {badge:'5 Thẻ hội viên', label:'GIÁ 80K/VÉ', name:'COMBO CHIỀU 15H–18H : 10 VÉ TẶNG 1 VÉ', price:720000, months:3, free:11},
    6: {badge:'6 Thẻ hội viên', label:'GIÁ 80K/VÉ', name:'COMBO CHIỀU 15H–18H : 20 VÉ TẶNG 2 VÉ', price:1440000, months:6, free:22},
};

let currentPlanId = null;

function registerPlan(planId) {
    currentPlanId = planId;
    const p = plansData[planId];

    // Fill payment modal
    document.getElementById('pm-plan-badge').textContent = p.badge;
    document.getElementById('pm-plan-label').textContent = p.label;
    document.getElementById('pm-plan-name').textContent  = p.name;
    document.getElementById('pm-months').textContent     = `⏱ ${p.months} Tháng`;
    document.getElementById('pm-free').textContent       = `🎁 Miễn phí ${p.free} vé`;
    document.getElementById('pm-price-sub').textContent  = p.price.toLocaleString('vi-VN') + 'đ';
    document.getElementById('pm-free-val').textContent   = `+${p.free} vé`;
    document.getElementById('pm-total').textContent      = p.price.toLocaleString('vi-VN') + 'đ';

    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

// Payment method selection
document.querySelectorAll('.pm-method-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.pm-method-card').forEach(c => {
            c.classList.remove('selected');
            c.querySelector('.pm-check').style.opacity = '0';
        });
        this.classList.add('selected');
        this.querySelector('.pm-check').style.opacity = '1';
        this.querySelector('input[type=radio]').checked = true;
    });
});

// Confirm payment
document.getElementById('btn-confirm-payment').addEventListener('click', function() {
    const method = document.querySelector('input[name=pm_method]:checked').value;
    const btn    = this;

    btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Đang xử lý...';
    btn.disabled  = true;

    const formData = new FormData();
    formData.append('plan_id', currentPlanId);
    formData.append('payment_method', method);

    fetch('api/membership.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Close payment modal
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();

                // Fill member card
                showMemberCard(data);
            } else {
                alert('Lỗi: ' + data.error);
            }
        })
        .catch(() => alert('Có lỗi xảy ra. Vui lòng thử lại.'))
        .finally(() => {
            btn.innerHTML = '<i class="fas fa-lock me-2"></i>Xác nhận đăng ký';
            btn.disabled  = false;
        });
});

function showMemberCard(data) {
    // Fill card info
    document.getElementById('mc-code').textContent   = data.member_code;
    document.getElementById('mc-name').textContent   = data.user_name;
    document.getElementById('mc-plan').textContent   = `${data.months} tháng`;
    document.getElementById('mc-start').textContent  = formatDate(data.start_date);
    document.getElementById('mc-end').textContent    = formatDate(data.end_date);
    document.getElementById('mc-tickets').textContent = `${data.plan_name} : ${data.plan_detail} — ${data.free_tickets} vé miễn phí`;

    // Generate QR code
    const qrContainer = document.getElementById('mc-qr');
    qrContainer.innerHTML = '';
    new QRCode(qrContainer, {
        text: `BADMINTONPRO|${data.member_code}|${data.user_name}|${data.end_date}`,
        width: 120,
        height: 120,
        colorDark: '#1a1a2e',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    // Show card modal
    setTimeout(() => {
        new bootstrap.Modal(document.getElementById('memberCardModal')).show();
    }, 400);
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
}

function printMemberCard() {
    window.print();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
