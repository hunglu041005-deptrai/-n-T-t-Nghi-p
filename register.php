<?php
require_once __DIR__ . '/includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif ($password !== $confirm) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Email đã được sử dụng.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $mysqli->prepare('INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)');
            $insert->bind_param('ssss', $name, $email, $hash, $phone);
            if ($insert->execute()) {
                $_SESSION['user_id'] = $insert->insert_id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'user';
                redirect('index.php');
            } else {
                $error = 'Đăng ký thất bại, thử lại sau.';
            }
            $insert->close();
        }
        $stmt->close();
    }
}
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-3">Đăng ký tài khoản</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo escape($error); ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" name="name" class="form-control" value="<?php echo escape($_POST['name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo escape($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SĐT</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo escape($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" name="confirm" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                </form>
                <p class="mt-3 text-center">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
