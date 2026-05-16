<?php
require_once __DIR__ . '/includes/functions.php';

// Chỉ admin mới được chạy
if (!isAdmin()) {
    die('Chỉ admin mới được thực hiện thao tác này!');
}

echo "<!DOCTYPE html>";
echo "<html><head><title>Sửa lỗi hệ thống đánh giá</title>";
echo "<meta charset='UTF-8'>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body class='bg-light'>";

echo "<div class='container mt-5'>";
echo "<div class='card shadow mx-auto' style='max-width: 600px;'>";
echo "<div class='card-header bg-danger text-white'>";
echo "<h3 class='mb-0'>🔧 Sửa lỗi hệ thống đánh giá</h3>";
echo "</div>";
echo "<div class='card-body'>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_reviews'])) {
    try {
        echo "<h5>Đang sửa lỗi...</h5>";
        
        // 1. Tạo bảng product_reviews
        $sql1 = "CREATE TABLE IF NOT EXISTS product_reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            user_id INT NOT NULL,
            order_id INT DEFAULT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            title VARCHAR(200) DEFAULT NULL,
            comment TEXT,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
            helpful_count INT NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_product_rating (product_id, rating),
            INDEX idx_user_reviews (user_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($mysqli->query($sql1)) {
            echo "<div class='alert alert-success'>✅ Tạo bảng product_reviews thành công</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Bảng product_reviews: " . $mysqli->error . "</div>";
        }

        // 2. Tạo bảng order_items
        $sql2 = "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            price DECIMAL(10,0) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($mysqli->query($sql2)) {
            echo "<div class='alert alert-success'>✅ Tạo bảng order_items thành công</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Bảng order_items: " . $mysqli->error . "</div>";
        }

        // 3. Thêm cột rating vào bảng products
        $sql3 = "ALTER TABLE products 
                ADD COLUMN IF NOT EXISTS average_rating DECIMAL(3,2) DEFAULT 0,
                ADD COLUMN IF NOT EXISTS review_count INT DEFAULT 0";
        
        if ($mysqli->query($sql3)) {
            echo "<div class='alert alert-success'>✅ Thêm cột rating vào bảng products thành công</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Cột rating: " . $mysqli->error . "</div>";
        }

        // 4. Thêm dữ liệu mẫu
        echo "<h5>Thêm dữ liệu đánh giá mẫu:</h5>";
        
        // Lấy sản phẩm và user
        $products = $mysqli->query("SELECT id, name FROM products LIMIT 3")->fetch_all(MYSQLI_ASSOC);
        $users = $mysqli->query("SELECT id, name FROM users WHERE role != 'admin' LIMIT 2")->fetch_all(MYSQLI_ASSOC);
        
        if (!empty($products) && !empty($users)) {
            $sample_reviews = [
                [5, 'Sản phẩm tuyệt vời!', 'Chất lượng rất tốt, đúng như mô tả. Giao hàng nhanh, đóng gói cẩn thận.'],
                [4, 'Hài lòng với sản phẩm', 'Sản phẩm ổn, chất lượng tốt. Giá cả hợp lý.'],
                [5, 'Rất đáng mua!', 'Mình đã sử dụng được 2 tuần, cảm thấy rất hài lòng.'],
                [3, 'Tạm ổn', 'Sản phẩm bình thường, không có gì đặc biệt.']
            ];
            
            $review_count = 0;
            foreach ($products as $product) {
                foreach ($users as $user) {
                    if ($review_count >= count($sample_reviews)) break;
                    
                    $review = $sample_reviews[$review_count];
                    
                    // Kiểm tra xem đã có review chưa
                    $check = $mysqli->prepare("SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ?");
                    $check->bind_param('ii', $product['id'], $user['id']);
                    $check->execute();
                    $exists = $check->get_result()->num_rows > 0;
                    $check->close();
                    
                    if (!$exists) {
                        $stmt = $mysqli->prepare("INSERT INTO product_reviews (product_id, user_id, rating, title, comment, status) VALUES (?, ?, ?, ?, ?, 'approved')");
                        $stmt->bind_param('iiiss', $product['id'], $user['id'], $review[0], $review[1], $review[2]);
                        
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-info'>📝 Thêm đánh giá cho '{$product['name']}' bởi '{$user['name']}'</div>";
                            $review_count++;
                        }
                        $stmt->close();
                    }
                }
            }
        }

        // 5. Cập nhật rating cho sản phẩm
        echo "<h5>Cập nhật rating cho sản phẩm:</h5>";
        $products_all = $mysqli->query("SELECT id, name FROM products")->fetch_all(MYSQLI_ASSOC);
        
        foreach ($products_all as $product) {
            // Tính rating trung bình
            $rating_query = $mysqli->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM product_reviews WHERE product_id = ? AND status = 'approved'");
            $rating_query->bind_param('i', $product['id']);
            $rating_query->execute();
            $rating_result = $rating_query->get_result()->fetch_assoc();
            $rating_query->close();
            
            $avg_rating = $rating_result['avg_rating'] ? round($rating_result['avg_rating'], 2) : 0;
            $review_count = $rating_result['count'];
            
            // Cập nhật vào bảng products
            $update = $mysqli->prepare("UPDATE products SET average_rating = ?, review_count = ? WHERE id = ?");
            $update->bind_param('dii', $avg_rating, $review_count, $product['id']);
            $update->execute();
            $update->close();
            
            if ($review_count > 0) {
                echo "<div class='alert alert-success'>⭐ Cập nhật rating cho '{$product['name']}': {$avg_rating}/5 ({$review_count} đánh giá)</div>";
            }
        }
        
        echo "<div class='alert alert-success border-0 mt-4'>";
        echo "<h5>🎉 Sửa lỗi hoàn tất!</h5>";
        echo "<p>Hệ thống đánh giá đã được thiết lập thành công.</p>";
        echo "<div class='mt-3'>";
        echo "<a href='equipment.php' class='btn btn-success me-2'>🛍️ Xem shop</a>";
        echo "<a href='product-detail.php?id=1' class='btn btn-primary me-2'>📄 Chi tiết sản phẩm</a>";
        echo "<a href='admin/reviews.php' class='btn btn-warning'>⭐ Quản lý đánh giá</a>";
        echo "</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Lỗi: " . $e->getMessage() . "</div>";
    }
} else {
    // Show form
    echo "<p><strong>Lỗi phát hiện:</strong> Bảng <code>product_reviews</code> chưa tồn tại.</p>";
    echo "<p>Script này sẽ:</p>";
    echo "<ul>";
    echo "<li>✅ Tạo bảng <code>product_reviews</code></li>";
    echo "<li>✅ Tạo bảng <code>order_items</code></li>";
    echo "<li>✅ Thêm cột <code>average_rating</code> và <code>review_count</code> vào bảng products</li>";
    echo "<li>✅ Thêm dữ liệu đánh giá mẫu</li>";
    echo "<li>✅ Cập nhật rating cho tất cả sản phẩm</li>";
    echo "</ul>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_reviews' class='btn btn-danger btn-lg w-100'>";
    echo "<i class='fas fa-wrench me-2'></i>Sửa lỗi ngay";
    echo "</button>";
    echo "</form>";
    
    echo "<div class='mt-3'>";
    echo "<a href='admin/dashboard.php' class='btn btn-secondary'>← Quay lại Dashboard</a>";
    echo "</div>";
}

echo "</div>";
echo "</div>";
echo "</div>";

echo "</body></html>";
?>