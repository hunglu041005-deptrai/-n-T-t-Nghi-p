<?php
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập và role coach
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Lấy thông tin coach từ session role hoặc từ user_id
$coach = null;

if ($_SESSION['role'] === 'coach') {
    $stmt = $mysqli->prepare('SELECT c.* FROM coaches c WHERE c.user_id = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $coach = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$coach) {
        // Tài khoản coach nhưng chưa có bản ghi coaches → redirect
        header('Location: ../login.php');
        exit;
    }
} elseif (isAdmin()) {
    // Admin xem dashboard của HLV cụ thể
    $coach_id_param = intval($_GET['coach_id'] ?? 0);
    if ($coach_id_param) {
        $stmt = $mysqli->prepare('SELECT * FROM coaches WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $coach_id_param);
        $stmt->execute();
        $coach = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
    if (!$coach) {
        // Admin không chỉ định coach → redirect admin panel
        header('Location: ../admin/dashboard.php');
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}

$coach_id = $coach['id'];

// Tuần hiện tại
$week_start = $_GET['week'] ?? date('Y-m-d', strtotime('monday this week'));
$week_end   = date('Y-m-d', strtotime($week_start . ' +6 days'));
$weekLabel  = date('d/m', strtotime($week_start)) . ' – ' . date('d/m/Y', strtotime($week_end));

// Lấy học viên tuần này
$students_stmt = $mysqli->prepare("
    SELECT tr.*, 
           DATE_FORMAT(tr.created_at, '%d/%m/%Y %H:%i') as registered_at_fmt
    FROM training_registrations tr
    WHERE tr.coach_id = ? AND tr.week_start = ?
    ORDER BY tr.created_at DESC
");
$students_stmt->bind_param('is', $coach_id, $week_start);
$students_stmt->execute();
$students = $students_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$students_stmt->close();

// Thống kê
$total_this_week = count($students);
$max_per_week    = $coach ? (int)$coach['max_students_per_week'] : 3;
$remaining       = max(0, $max_per_week - $total_this_week);
$is_full         = $remaining <= 0;

// Lấy tất cả học viên của coach
$all_stmt = $mysqli->prepare("
    SELECT tr.*, DATE_FORMAT(tr.created_at, '%d/%m/%Y') as reg_date
    FROM training_registrations tr
    WHERE tr.coach_id = ?
    ORDER BY tr.week_start DESC, tr.created_at DESC
");
$all_stmt->bind_param('i', $coach_id);
$all_stmt->execute();
$all_students = $all_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$all_stmt->close();

$isAdminPage = true;
require_once __DIR__ . '/../includes/header.php';
?>

<style>
.coach-page { background: #f8fafc; min-height: 100vh; }

.coach-hero {
    background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    color: #fff;
    padding: 2rem;
    margin-bottom: 2rem;
    border-radius: 0 0 20px 20px;
}

.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    border: 1px solid #f0f0f0;
    height: 100%;
    transition: transform .2s;
}
.stat-card:hover { transform: translateY(-3px); }
.stat-number { font-size: 2rem; font-weight: 900; }

.student-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.2rem 1.4rem;
    box-shadow: 0 4px 15px rgba(0,0,0,.06);
    border: 1px solid #f0f0f0;
    margin-bottom: 1rem;
    transition: all .2s;
    position: relative;
}
.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,.1);
}

.day-badge {
    display: inline-flex; align-items: center;
    background: rgba(99,102,241,.1); color: #6366f1;
    border-radius: 8px; padding: 3px 10px;
    font-size: .78rem; font-weight: 700;
    margin: 2px;
}

.slot-bar {
    height: 10px; border-radius: 5px;
    background: #e5e7eb; overflow: hidden;
}
.slot-bar-fill {
    height: 100%; border-radius: 5px;
    transition: width .5s ease;
}

.week-nav {
    display: flex; align-items: center; gap: 1rem;
}
.week-nav a {
    display: flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,.15); color: #fff;
    text-decoration: none; transition: background .2s;
}
.week-nav a:hover { background: rgba(255,255,255,.3); }

.qr-modal-content {
    text-align: center;
    padding: 2rem;
}

.tab-btn {
    background: transparent; border: none;
    padding: .6rem 1.4rem; border-radius: 10px;
    font-weight: 600; color: #6b7280; cursor: pointer;
    transition: all .2s;
}
.tab-btn.active {
    background: linear-gradient(135deg, #302b63, #24243e);
    color: #fff;
    box-shadow: 0 4px 15px rgba(48,43,99,.3);
}

.empty-state {
    text-align: center; padding: 3rem; color: #9ca3af;
}
.empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: .3; display: block; }

.full-badge {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff; border-radius: 20px; padding: 4px 12px;
    font-size: .78rem; font-weight: 700;
}
.available-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: #fff; border-radius: 20px; padding: 4px 12px;
    font-size: .78rem; font-weight: 700;
}
</style>

<div class="coach-page">
    <!-- Hero -->
    <div class="coach-hero">
        <div class="container-lg">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div style="width:52px;height:52px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-chalkboard-teacher fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo escape($coach['name'] ?? 'HLV Dashboard'); ?></h3>
                            <small style="opacity:.7;"><?php echo escape($coach['specialty'] ?? 'Huấn luyện viên cầu lông'); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Tuần điều hướng -->
                <div class="col-md-6">
                    <div class="week-nav justify-content-end">
                        <a href="?week=<?php echo date('Y-m-d', strtotime($week_start . ' -7 days')); ?><?php echo $coach_id && isAdmin() ? '&coach_id='.$coach_id : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <div class="text-center">
                            <div class="fw-bold">Tuần <?php echo $weekLabel; ?></div>
                            <small style="opacity:.6;">
                                <?php echo $total_this_week; ?>/<?php echo $max_per_week; ?> học viên
                                &nbsp;·&nbsp;
                                <?php if ($is_full): ?>
                                    <span class="full-badge">ĐÃ ĐẦY</span>
                                <?php else: ?>
                                    <span class="available-badge">CÒN <?php echo $remaining; ?> CHỖ</span>
                                <?php endif; ?>
                            </small>
                        </div>
                        <a href="?week=<?php echo date('Y-m-d', strtotime($week_start . ' +7 days')); ?><?php echo $coach_id && isAdmin() ? '&coach_id='.$coach_id : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="?<?php echo $coach_id && isAdmin() ? 'coach_id='.$coach_id : ''; ?>" title="Tuần này" style="background:rgba(251,191,36,.3);">
                            <i class="fas fa-home" style="font-size:.8rem;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slot bar -->
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <small style="opacity:.7;">Lịch tuần này</small>
                    <small style="opacity:.7;"><?php echo $total_this_week; ?>/<?php echo $max_per_week; ?> học viên</small>
                </div>
                <div class="slot-bar">
                    <?php
                    $pct = $max_per_week > 0 ? min(100, round($total_this_week / $max_per_week * 100)) : 0;
                    $barColor = $is_full ? '#ef4444' : ($pct >= 66 ? '#f59e0b' : '#28a745');
                    ?>
                    <div class="slot-bar-fill" style="width:<?php echo $pct; ?>%;background:<?php echo $barColor; ?>;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-lg pb-5">
        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-primary"><?php echo $total_this_week; ?></div>
                    <div style="color:#6b7280;font-size:.85rem;">Học viên tuần này</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number" style="color:<?php echo $is_full ? '#ef4444' : '#28a745'; ?>;"><?php echo $remaining; ?></div>
                    <div style="color:#6b7280;font-size:.85rem;">Chỗ còn trống</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-warning"><?php echo count($all_students); ?></div>
                    <div style="color:#6b7280;font-size:.85rem;">Tổng học viên</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number text-info">3</div>
                    <div style="color:#6b7280;font-size:.85rem;">Buổi/tuần/học viên</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div style="background:#fff;border-radius:14px;padding:.5rem;margin-bottom:1.5rem;box-shadow:0 2px 10px rgba(0,0,0,.06);display:inline-flex;gap:.25rem;">
            <button class="tab-btn active" onclick="switchTab('week', this)">
                <i class="fas fa-calendar-week me-2"></i>Tuần này
            </button>
            <button class="tab-btn" onclick="switchTab('all', this)">
                <i class="fas fa-users me-2"></i>Tất cả học viên
            </button>
            <button class="tab-btn" onclick="switchTab('schedule', this)">
                <i class="fas fa-calendar-alt me-2"></i>Lịch học
            </button>
            <button class="tab-btn" onclick="switchTab('scan', this)">
                <i class="fas fa-qrcode me-2"></i>Quét QR
            </button>
        </div>

        <!-- Tab: Tuần này -->
        <div id="tab-week">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Học viên tuần <?php echo $weekLabel; ?></h5>
                <?php if ($is_full): ?>
                    <span class="full-badge"><i class="fas fa-ban me-1"></i>Đã đầy — Không nhận thêm</span>
                <?php else: ?>
                    <span class="available-badge"><i class="fas fa-door-open me-1"></i>Còn <?php echo $remaining; ?> chỗ trống</span>
                <?php endif; ?>
            </div>

            <?php if (empty($students)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h5>Chưa có học viên tuần này</h5>
                <p>Học viên đăng ký sẽ hiện ở đây</p>
            </div>
            <?php else: ?>
            <?php foreach ($students as $s):
                $days = array_map('trim', explode(',', $s['schedule_days'] ?? ''));
                $dayNames = ['Mon'=>'T2','Tue'=>'T3','Wed'=>'T4','Thu'=>'T5','Fri'=>'T6','Sat'=>'T7','Sun'=>'CN'];
            ?>
            <div class="student-card">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:44px;height:44px;background:linear-gradient(135deg,#302b63,#24243e);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo escape($s['student_name']); ?></div>
                                <small class="text-muted"><?php echo escape($s['phone']); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fw-bold" style="font-size:.85rem;color:#6366f1;"><?php echo escape($s['course'] === 'beginner' ? 'Cơ bản' : ($s['course'] === 'intermediate' ? 'Trung cấp' : 'Nâng cao')); ?></div>
                        <small class="text-muted"><?php echo escape($s['schedule_time'] ?? ''); ?></small>
                    </div>
                    <div class="col-md-3">
                        <?php foreach ($days as $d): ?>
                            <span class="day-badge"><?php echo $dayNames[$d] ?? $d; ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-sm btn-outline-primary" onclick="showStudentQR('<?php echo escape($s['student_code']); ?>', '<?php echo escape($s['student_name']); ?>', '<?php echo escape($s['student_code']); ?>')">
                            <i class="fas fa-qrcode me-1"></i>QR
                        </button>
                        <div class="text-muted mt-1" style="font-size:.72rem;"><?php echo $s['registered_at_fmt']; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Tab: Tất cả -->
        <div id="tab-all" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Tất cả học viên (<?php echo count($all_students); ?>)</h5>
                <input type="text" id="studentSearch" class="form-control form-control-sm" placeholder="Tìm học viên..." style="width:200px;" oninput="filterStudents(this.value)">
            </div>

            <?php if (empty($all_students)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h5>Chưa có học viên nào</h5>
            </div>
            <?php else: ?>
            <div id="allStudentsList">
            <?php foreach ($all_students as $s):
                $days = array_map('trim', explode(',', $s['schedule_days'] ?? ''));
                $dayNames = ['Mon'=>'T2','Tue'=>'T3','Wed'=>'T4','Thu'=>'T5','Fri'=>'T6','Sat'=>'T7','Sun'=>'CN'];
            ?>
            <div class="student-card student-item">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="fw-bold"><?php echo escape($s['student_name']); ?></div>
                        <small class="text-muted"><?php echo escape($s['phone']); ?></small>
                    </div>
                    <div class="col-md-2">
                        <span style="background:#eff6ff;color:#3b82f6;border-radius:6px;padding:2px 8px;font-size:.78rem;font-weight:700;">
                            <?php echo $s['course'] === 'beginner' ? 'Cơ bản' : ($s['course'] === 'intermediate' ? 'Trung cấp' : 'Nâng cao'); ?>
                        </span>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted"><i class="fas fa-calendar me-1"></i><?php echo date('d/m/Y', strtotime($s['week_start'])); ?></small>
                    </div>
                    <div class="col-md-3">
                        <?php foreach ($days as $d): ?>
                            <span class="day-badge"><?php echo $dayNames[$d] ?? $d; ?></span>
                        <?php endforeach; ?>
                        <div style="font-size:.72rem;color:#9ca3af;margin-top:2px;"><?php echo escape($s['schedule_time'] ?? ''); ?></div>
                    </div>
                    <div class="col-md-2 text-end">
                        <div class="fw-bold" style="font-size:.78rem;font-family:monospace;color:#6366f1;"><?php echo escape($s['student_code']); ?></div>
                        <button class="btn btn-xs btn-outline-primary mt-1" style="font-size:.72rem;padding:2px 8px;" onclick="showStudentQR('<?php echo escape($s['student_code']); ?>', '<?php echo escape($s['student_name']); ?>', '<?php echo escape($s['student_code']); ?>')">
                            <i class="fas fa-qrcode me-1"></i>QR
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Lịch học -->
        <div id="tab-schedule" style="display:none;">
            <h5 class="fw-bold mb-4">Lịch học tuần <?php echo $weekLabel; ?></h5>
            <?php
            $dayMap = ['Mon'=>'Thứ 2','Tue'=>'Thứ 3','Wed'=>'Thứ 4','Thu'=>'Thứ 5','Fri'=>'Thứ 6','Sat'=>'Thứ 7'];
            $scheduleGrid = [];
            foreach ($students as $s) {
                $days = array_map('trim', explode(',', $s['schedule_days'] ?? ''));
                foreach ($days as $d) {
                    $scheduleGrid[$d][] = $s;
                }
            }
            ?>
            <div class="row g-3">
                <?php foreach ($dayMap as $dayKey => $dayName): ?>
                <div class="col-md-4 col-sm-6">
                    <div style="background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.06);">
                        <div style="background:linear-gradient(135deg,#302b63,#24243e);color:#fff;padding:.8rem 1rem;">
                            <div class="fw-bold"><?php echo $dayName; ?></div>
                            <small style="opacity:.7;"><?php echo date('d/m', strtotime($week_start . ' +' . (array_search($dayKey, array_keys($dayMap))) . ' days')); ?></small>
                        </div>
                        <div style="padding:.8rem;">
                            <?php if (!empty($scheduleGrid[$dayKey])): ?>
                                <?php foreach ($scheduleGrid[$dayKey] as $s): ?>
                                <div style="background:#f8fafc;border-radius:8px;padding:.5rem .7rem;margin-bottom:.4rem;border-left:3px solid #6366f1;">
                                    <div class="fw-bold" style="font-size:.82rem;"><?php echo escape($s['student_name']); ?></div>
                                    <small style="color:#6b7280;"><?php echo escape($s['schedule_time'] ?? ''); ?></small>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align:center;padding:.8rem;color:#9ca3af;font-size:.82rem;">Trống</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tab: Quét QR -->
        <div id="tab-scan" style="display:none;">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div style="background:#fff;border-radius:20px;padding:2rem;box-shadow:0 4px 20px rgba(0,0,0,.08);text-align:center;">
                        <div style="width:64px;height:64px;background:linear-gradient(135deg,#302b63,#6366f1);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;color:#fff;">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Quét mã học viên</h5>
                        <p class="text-muted mb-4">Nhập mã học viên để kiểm tra thông tin</p>

                        <div class="input-group mb-3">
                            <input type="text" id="scanInput" class="form-control form-control-lg"
                                   placeholder="Nhập mã HV... (VD: HV2026ABCDEF)"
                                   style="border-radius:12px 0 0 12px;text-transform:uppercase;font-family:monospace;">
                            <button class="btn btn-primary" onclick="lookupStudent()" style="border-radius:0 12px 12px 0;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <div id="scanResult" style="display:none;margin-top:1rem;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal hiển thị QR học viên -->
<div class="modal fade" id="studentQRModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#0f0c29,#302b63);padding:1.2rem 1.5rem;text-align:center;color:#fff;">
                <h5 class="fw-bold mb-0" id="qrModalTitle">Thẻ học viên</h5>
            </div>
            <div style="padding:1.5rem;text-align:center;">
                <div id="qrModalCode" style="font-size:1.1rem;font-weight:900;font-family:monospace;color:#302b63;margin-bottom:1rem;letter-spacing:2px;"></div>
                <div id="qrModalQR" style="display:inline-block;background:#fff;padding:10px;border-radius:12px;border:2px solid #f0f0f0;"></div>
                <div class="mt-3" style="font-size:.8rem;color:#6b7280;">
                    <i class="fas fa-info-circle me-1"></i>Quét để xác nhận học viên vào sân
                </div>
            </div>
            <div style="padding:1rem;border-top:1px solid #f0f0f0;display:flex;gap:.5rem;">
                <button onclick="printQR()" class="btn flex-grow-1" style="background:linear-gradient(135deg,#302b63,#24243e);color:#fff;border:none;border-radius:10px;font-weight:600;">
                    <i class="fas fa-print me-2"></i>In QR
                </button>
                <button class="btn btn-outline-secondary" style="border-radius:10px;" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #qrModalQR, #qrModalQR * { visibility: visible; }
    #qrModalQR { position: fixed; top: 50%; left: 50%; transform: translate(-50%,-50%); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
// Switch tabs
function switchTab(tabName, btn) {
    document.querySelectorAll('[id^="tab-"]').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tabName).style.display = 'block';
    btn.classList.add('active');
}

// Hiển thị QR modal
function showStudentQR(code, name, qrData) {
    document.getElementById('qrModalTitle').textContent = name;
    document.getElementById('qrModalCode').textContent = code;

    const qrEl = document.getElementById('qrModalQR');
    qrEl.innerHTML = '';
    new QRCode(qrEl, {
        text: 'BADMINTONPRO-HV|' + code + '|' + name,
        width: 180, height: 180,
        colorDark: '#0f0c29', colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    new bootstrap.Modal(document.getElementById('studentQRModal')).show();
}

// In QR
function printQR() {
    window.print();
}

// Lọc học viên
function filterStudents(q) {
    document.querySelectorAll('.student-item').forEach(el => {
        el.style.display = el.textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}

// Tra cứu mã học viên (quét QR)
function lookupStudent() {
    const code = document.getElementById('scanInput').value.trim().toUpperCase();
    const resultEl = document.getElementById('scanResult');

    if (!code) return;

    resultEl.style.display = 'block';
    resultEl.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div></div>';

    fetch('<?php echo '../'; ?>api/scan-student.php?code=' + encodeURIComponent(code))
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const s = data.student;
                resultEl.innerHTML = `
                    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:14px;padding:1.2rem;text-align:left;">
                        <div style="display:flex;align-items:center;gap:.8rem;margin-bottom:1rem;">
                            <div style="width:44px;height:44px;background:linear-gradient(135deg,#28a745,#20c997);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-check text-white fa-lg"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-success">✅ Xác nhận hợp lệ</div>
                                <small class="text-muted">Mã: ${s.student_code}</small>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;font-size:.85rem;">
                            <div><strong>Họ tên:</strong> ${s.student_name}</div>
                            <div><strong>SĐT:</strong> ${s.phone}</div>
                            <div><strong>Khóa học:</strong> ${s.course_label}</div>
                            <div><strong>HLV:</strong> ${s.coach_name}</div>
                            <div style="grid-column:span 2;"><strong>Lịch:</strong> ${s.schedule_days || ''} · ${s.schedule_time || ''}</div>
                        </div>
                    </div>
                `;
            } else {
                resultEl.innerHTML = `
                    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:14px;padding:1.2rem;text-align:center;">
                        <i class="fas fa-times-circle text-danger fa-2x mb-2 d-block"></i>
                        <div class="fw-bold text-danger">Không tìm thấy học viên</div>
                        <small class="text-muted">Mã "${code}" không hợp lệ</small>
                    </div>
                `;
            }
        })
        .catch(() => {
            resultEl.innerHTML = '<div class="alert alert-danger">Lỗi kết nối</div>';
        });
}

// Enter để tìm kiếm
document.getElementById('scanInput')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') lookupStudent();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
