<?php
require_once __DIR__ . '/includes/functions.php';

// Chỉ admin mới được chạy
if (!isAdmin()) {
    die('Chỉ admin mới được thực hiện thao tác này!');
}

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug Reviews System</title>";
echo "<meta charset='UTF-8'>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body class='bg-light'>";

echo "<div class='container mt-5'>";
echo "<div class='card shadow mx-auto' style='max-width: 800px;'>";
echo "<div class='card-header bg-info text-white'>";
echo "<h3 class='mb-0'>🔍 Debug Reviews System</h3>";
echo "</div>";
echo "<div class='card-body'>";

echo "<h5>1. Kiểm tra bảng database:</h5>";

// Check tables
$tables_to_check = ['product_reviews', 'products', 'users'];
foreach ($tables_to_check as $table) {
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<div class='alert alert-success'>✅ Bảng <code>$table</code> tồn tại</div>";
        
        // Show table structure
        $structure = $mysqli->query("DESCRIBE $table");
        if ($structure) {
            echo "<details><summary>Cấu trúc bảng $table</summary>";
            echo "<table class='table table-sm'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $structure->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "</tr>";
            }
            echo "</table></details>";
        }
    } else {
        echo "<div class='alert alert-danger'>❌ Bảng <code>$table</code> không tồn tại</div>";
    }
}

echo "<h5>2. Kiểm tra dữ liệu:</h5>";

// Check products
$products_count = $mysqli->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
echo "<div class='alert alert-info'>📦 Có $products_count sản phẩm</div>";

// Check users
$users_count = $mysqli->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
echo "<div class='alert alert-info'>👥 Có $users_count người dùng</div>";

// Check reviews if table exists
$reviews_result = $mysqli->query("SHOW TABLES LIKE 'product_reviews'");
if ($reviews_result && $reviews_result->num_rows > 0) {
    $reviews_count = $mysqli->query("SELECT COUNT(*) as count FROM product_reviews")->fetch_assoc()['count'];
    echo "<div class='alert alert-info'>⭐ Có $reviews_count đánh giá</div>";
    
    // Show recent reviews
    $recent_reviews = $mysqli->query("SELECT pr.*, u.name as user_name, p.name as product_name FROM product_reviews pr JOIN users u ON pr.user_id = u.id JOIN products p ON pr.product_id = p.id ORDER BY pr.created_at DESC LIMIT 5");
    if ($recent_reviews && $recent_reviews->num_rows > 0) {
        echo "<h6>Đánh giá gần đây:</h6>";
        echo "<table class='table table-sm'>";
        echo "<tr><th>Sản phẩm</th><th>Người dùng</th><th>Rating</th><th>Nhận xét</th><th>Ngày</th></tr>";
        while ($review = $recent_reviews->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$review['product_name']}</td>";
            echo "<td>{$review['user_name']}</td>";
            echo "<td>{$review['rating']}/5</td>";
            echo "<td>" . substr($review['comment'], 0, 50) . "...</td>";
            echo "<td>{$review['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<div class='alert alert-warning'>⚠️ Bảng product_reviews chưa tồn tại</div>";
}

echo "<h5>3. Kiểm tra session:</h5>";
echo "<div class='alert alert-info'>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'Chưa đăng nhập') . "<br>";
echo "Role: " . ($_SESSION['role'] ?? 'Không có') . "<br>";
echo "Name: " . ($_SESSION['name'] ?? 'Không có') . "<br>";
echo "</div>";

echo "<h5>4. Thao tác:</h5>";
echo "<div class='d-flex gap-2'>";
echo "<a href='fix-reviews-setup.php' class='btn btn-warning'>🔧 Chạy Setup Reviews</a>";
echo "<a href='product-detail.php?id=1' class='btn btn-primary'>📄 Test Product Detail</a>";
echo "<a href='admin/dashboard.php' class='btn btn-secondary'>← Dashboard</a>";
echo "</div>";

echo "</div>";
echo "</div>";
echo "</div>";

echo "</body></html>";
?>