<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Logout</title>";
echo "<meta charset='UTF-8'>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body class='bg-light'>";

echo "<div class='container mt-5'>";
echo "<div class='card shadow mx-auto' style='max-width: 500px;'>";
echo "<div class='card-header bg-danger text-white text-center'>";
echo "<h4 class='mb-0'>🚪 Test Logout</h4>";
echo "</div>";
echo "<div class='card-body text-center'>";

echo "<div class='mb-4'>";
echo "<h5>Thông tin hiện tại:</h5>";
echo "<p><strong>User:</strong> " . ($_SESSION['name'] ?? 'N/A') . "</p>";
echo "<p><strong>Role:</strong> " . ($_SESSION['role'] ?? 'N/A') . "</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "</div>";

echo "<div class='d-grid gap-2'>";
echo "<a href='logout.php' class='btn btn-danger btn-lg'>";
echo "<i class='fas fa-sign-out-alt me-2'></i>Đăng xuất ngay";
echo "</a>";

echo "<a href='dashboard.php' class='btn btn-secondary'>";
echo "<i class='fas fa-arrow-left me-2'></i>Quay lại Dashboard";
echo "</a>";
echo "</div>";

echo "<hr>";
echo "<small class='text-muted'>";
echo "File logout.php sẽ xóa session và redirect về login.php";
echo "</small>";

echo "</div>";
echo "</div>";
echo "</div>";

echo "</body></html>";
?>