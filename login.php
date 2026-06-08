<?php
require_once __DIR__ . '/includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Vui lòng điền email và mật khẩu.';
    } else {
        $stmt = $mysqli->prepare('SELECT id, name, email, password, role, status FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Email hoặc mật khẩu không đúng.';
        } elseif ($user['status'] != 1) {
            $error = 'Tài khoản đang bị khóa.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
                exit;
            } elseif ($user['role'] === 'coach') {
                header('Location: coach/dashboard.php');
                exit;
            } else {
                // Redirect về trang trước nếu có
                $redirect = $_GET['redirect'] ?? 'index.php';
                if (strpos($redirect, 'http') === false) {
                    header('Location: ' . $redirect);
                } else {
                    header('Location: index.php');
                }
                exit;
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                <i class="fas fa-badminton text-primary fa-2x"></i>
                            </div>
                            <h2 class="fw-bold text-dark mb-2">Đăng nhập</h2>
                            <p class="text-muted">Chào mừng bạn quay trở lại!</p>
                        </div>
                        
                        <?php if (isset($_GET['message']) && $_GET['message'] === 'logout_success'): ?>
                            <div class="alert alert-success border-0 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>Đăng xuất thành công!</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span><?php echo htmlspecialchars($error); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="fas fa-envelope text-muted me-2"></i>Email
                                </label>
                                <input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                       placeholder="Nhập địa chỉ email của bạn" 
                                       style="border-radius: 15px; background-color: #f8f9fa;" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="fas fa-lock text-muted me-2"></i>Mật khẩu
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control form-control-lg border-0 shadow-sm" 
                                           placeholder="Nhập mật khẩu" 
                                           style="border-radius: 15px 0 0 15px; background-color: #f8f9fa;" 
                                           required id="passwordInput">
                                    <button class="btn btn-outline-secondary border-0 shadow-sm" type="button" id="togglePassword"
                                            style="border-radius: 0 15px 15px 0; background-color: #f8f9fa;">
                                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Admin Login Info -->
                            <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 15px;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>Đăng nhập Admin:</strong> 
                                        <small class="d-block text-muted">Sử dụng tài khoản admin để truy cập trang quản trị</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label text-muted" for="rememberMe">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a href="#" class="text-decoration-none small text-primary fw-bold" 
                                   data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                    Quên mật khẩu?
                                </a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm hover-lift" 
                                    style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Chưa có tài khoản? 
                                <a href="register.php" class="text-decoration-none fw-bold text-primary">
                                    Đăng ký ngay <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </p>
                        </div>

                        <!-- Demo Accounts -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="text-center text-muted mb-3">
                                <i class="fas fa-key me-2"></i>Tài khoản demo
                            </h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                                            onclick="fillDemoAccount('admin')">
                                        <i class="fas fa-user-shield me-1"></i>Admin
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                            onclick="fillDemoAccount('user')">
                                        <i class="fas fa-user me-1"></i>User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-key text-warning me-2"></i>Quên mật khẩu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted">Nhập email của bạn để nhận liên kết đặt lại mật khẩu.</p>
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control form-control-lg border-0 shadow-sm" 
                               id="forgotEmail" placeholder="Nhập địa chỉ email" 
                               style="border-radius: 15px; background-color: #f8f9fa;" required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm" 
                            style="border-radius: 15px;">
                        <i class="fas fa-paper-plane me-2"></i>Gửi liên kết
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'password') {
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        } else {
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        }
    });
    
    // Forgot password form
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('forgotEmail').value;
        
        // Simulate sending reset email
        alert('Liên kết đặt lại mật khẩu đã được gửi đến ' + email);
        bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal')).hide();
    });
});

// Fill demo account credentials
function fillDemoAccount(type) {
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    
    if (type === 'admin') {
        emailInput.value = 'admin@badminton.local';
        passwordInput.value = 'admin123';
    } else {
        emailInput.value = 'user@example.com';
        passwordInput.value = 'password123';
    }
    
    // Add visual feedback
    emailInput.style.backgroundColor = '#e8f5e8';
    passwordInput.style.backgroundColor = '#e8f5e8';
    
    setTimeout(() => {
        emailInput.style.backgroundColor = '#f8f9fa';
        passwordInput.style.backgroundColor = '#f8f9fa';
    }, 1000);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
