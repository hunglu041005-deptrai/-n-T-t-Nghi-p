<?php
require_once __DIR__ . '/includes/functions.php';

// Chỉ admin mới được chạy
if (!isAdmin()) {
    die('Chỉ admin mới được thực hiện thao tác này!');
}

echo "<!DOCTYPE html>";
echo "<html><head><title>Thêm sản phẩm mẫu</title>";
echo "<meta charset='UTF-8'>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body class='bg-light'>";

echo "<div class='container mt-5'>";
echo "<div class='row justify-content-center'>";
echo "<div class='col-md-8'>";

echo "<div class='card shadow'>";
echo "<div class='card-header bg-primary text-white'>";
echo "<h3 class='mb-0'>🛍️ Thêm sản phẩm mẫu</h3>";
echo "</div>";
echo "<div class='card-body'>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_samples'])) {
    try {
        // Sample categories
        $categories = [
            ['Vợt cầu lông', 'vot-cau-long', 'Vợt cầu lông chính hãng từ các thương hiệu uy tín'],
            ['Giày thể thao', 'giay-the-thao', 'Giày cầu lông chuyên dụng với công nghệ tiên tiến'],
            ['Quần áo', 'quan-ao', 'Quần áo thể thao cầu lông thoáng mát'],
            ['Phụ kiện', 'phu-kien', 'Túi đựng vợt, cước và các phụ kiện khác']
        ];

        $category_ids = [];
        
        // Add categories
        foreach ($categories as $cat) {
            $check = $mysqli->prepare("SELECT id FROM product_categories WHERE slug = ?");
            $check->bind_param('s', $cat[1]);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows > 0) {
                $category_ids[$cat[1]] = $result->fetch_assoc()['id'];
                echo "<div class='alert alert-info'>ℹ️ Danh mục '{$cat[0]}' đã tồn tại</div>";
            } else {
                $stmt = $mysqli->prepare("INSERT INTO product_categories (name, slug, description, status) VALUES (?, ?, ?, 1)");
                $stmt->bind_param('sss', $cat[0], $cat[1], $cat[2]);
                if ($stmt->execute()) {
                    $category_ids[$cat[1]] = $mysqli->insert_id;
                    echo "<div class='alert alert-success'>✅ Thêm danh mục '{$cat[0]}' thành công</div>";
                }
                $stmt->close();
            }
            $check->close();
        }

        // Sample products
        $products = [
            // Vợt cầu lông
            [
                'category' => 'vot-cau-long',
                'name' => 'Vợt Yonex Arcsaber 11',
                'description' => 'Vợt cầu lông cao cấp với công nghệ Sonic Metal, phù hợp cho người chơi tấn công',
                'brand' => 'Yonex',
                'price' => 3200000,
                'sale_price' => 2800000,
                'sku' => 'YNX-ARC11',
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=400&fit=crop',
                'featured' => 1
            ],
            [
                'category' => 'vot-cau-long',
                'name' => 'Vợt Victor Thruster K9900',
                'description' => 'Vợt tấn công mạnh mẽ với trọng lượng cân bằng, phù hợp cho người chơi trung bình',
                'brand' => 'Victor',
                'price' => 2800000,
                'sale_price' => null,
                'sku' => 'VCT-TK9900',
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?w=400&h=400&fit=crop',
                'featured' => 0
            ],
            [
                'category' => 'vot-cau-long',
                'name' => 'Vợt Lining Windstorm 78',
                'description' => 'Vợt nhẹ với khả năng kiểm soát tốt, phù hợp cho người mới bắt đầu',
                'brand' => 'Lining',
                'price' => 2400000,
                'sale_price' => 2100000,
                'sku' => 'LN-WS78',
                'stock' => 20,
                'image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop',
                'featured' => 1
            ],
            
            // Giày thể thao
            [
                'category' => 'giay-the-thao',
                'name' => 'Giày Yonex Power Cushion 65Z3',
                'description' => 'Giày cầu lông với công nghệ đệm Power Cushion, hỗ trợ tối đa cho chân',
                'brand' => 'Yonex',
                'price' => 2200000,
                'sale_price' => null,
                'sku' => 'YNX-PC65Z3',
                'stock' => 25,
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
                'featured' => 1
            ],
            [
                'category' => 'giay-the-thao',
                'name' => 'Giày Victor A922',
                'description' => 'Giày nhẹ với độ bám tốt, thiết kế thời trang và bền bỉ',
                'brand' => 'Victor',
                'price' => 1800000,
                'sale_price' => 1600000,
                'sku' => 'VCT-A922',
                'stock' => 18,
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop',
                'featured' => 0
            ],
            
            // Quần áo
            [
                'category' => 'quan-ao',
                'name' => 'Áo Yonex 10274EX',
                'description' => 'Áo thể thao thoáng mát với công nghệ thấm hút mồ hôi, phù hợp thi đấu',
                'brand' => 'Yonex',
                'price' => 450000,
                'sale_price' => 380000,
                'sku' => 'YNX-10274EX',
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=400&fit=crop',
                'featured' => 0
            ],
            [
                'category' => 'quan-ao',
                'name' => 'Quần Victor R-3096',
                'description' => 'Quần short thể thao với chất liệu co giãn tốt, thoải mái khi vận động',
                'brand' => 'Victor',
                'price' => 380000,
                'sale_price' => null,
                'sku' => 'VCT-R3096',
                'stock' => 22,
                'image' => 'https://images.unsplash.com/photo-1506629905607-d9f02a6a0e35?w=400&h=400&fit=crop',
                'featured' => 0
            ],
            
            // Phụ kiện
            [
                'category' => 'phu-kien',
                'name' => 'Túi đựng vợt Yonex BAG92026EX',
                'description' => 'Túi đựng vợt cao cấp với nhiều ngăn tiện lợi, chứa được 6 cây vợt',
                'brand' => 'Yonex',
                'price' => 1200000,
                'sale_price' => null,
                'sku' => 'YNX-BAG92026',
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop',
                'featured' => 1
            ],
            [
                'category' => 'phu-kien',
                'name' => 'Cước Victor VBS-68P',
                'description' => 'Cước cầu lông chất lượng cao với độ bền tốt, phù hợp thi đấu chuyên nghiệp',
                'brand' => 'Victor',
                'price' => 280000,
                'sale_price' => 250000,
                'sku' => 'VCT-VBS68P',
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop',
                'featured' => 0
            ]
        ];

        // Add products
        $added_count = 0;
        foreach ($products as $product) {
            $category_id = $category_ids[$product['category']];
            $slug = strtolower(str_replace(' ', '-', $product['name']));
            
            // Check if product exists
            $check = $mysqli->prepare("SELECT id FROM products WHERE sku = ?");
            $check->bind_param('s', $product['sku']);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows > 0) {
                echo "<div class='alert alert-warning'>⚠️ Sản phẩm '{$product['name']}' đã tồn tại</div>";
            } else {
                $stmt = $mysqli->prepare("INSERT INTO products (category_id, name, slug, description, brand, price, sale_price, sku, stock_quantity, image, featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->bind_param('issssiisisi', 
                    $category_id, 
                    $product['name'], 
                    $slug, 
                    $product['description'], 
                    $product['brand'], 
                    $product['price'], 
                    $product['sale_price'], 
                    $product['sku'], 
                    $product['stock'], 
                    $product['image'], 
                    $product['featured']
                );
                
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>✅ Thêm sản phẩm '{$product['name']}' thành công</div>";
                    $added_count++;
                } else {
                    echo "<div class='alert alert-danger'>❌ Lỗi thêm sản phẩm '{$product['name']}': " . $mysqli->error . "</div>";
                }
                $stmt->close();
            }
            $check->close();
        }

        echo "<div class='alert alert-success border-0 mt-4'>";
        echo "<h5>🎉 Hoàn tất!</h5>";
        echo "<p>Đã thêm <strong>$added_count</strong> sản phẩm mẫu vào hệ thống.</p>";
        echo "<div class='mt-3'>";
        echo "<a href='admin/shop.php' class='btn btn-primary me-2'>🛠️ Quản lý Shop</a>";
        echo "<a href='equipment.php' class='btn btn-success me-2'>🛍️ Xem trang khách hàng</a>";
        echo "<a href='test-shop-sync.php' class='btn btn-info'>📊 Test Sync</a>";
        echo "</div>";
        echo "</div>";

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Lỗi: " . $e->getMessage() . "</div>";
    }
} else {
    // Show form
    echo "<p>Thêm dữ liệu mẫu để test hệ thống shop:</p>";
    echo "<ul>";
    echo "<li>4 danh mục sản phẩm</li>";
    echo "<li>10 sản phẩm mẫu với hình ảnh thật</li>";
    echo "<li>Giá cả và thông tin chi tiết</li>";
    echo "<li>Một số sản phẩm có giá khuyến mãi</li>";
    echo "</ul>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='add_samples' class='btn btn-primary btn-lg'>";
    echo "<i class='fas fa-plus me-2'></i>Thêm sản phẩm mẫu";
    echo "</button>";
    echo "</form>";
    
    echo "<div class='mt-4'>";
    echo "<a href='admin/dashboard.php' class='btn btn-secondary'>← Quay lại Dashboard</a>";
    echo "</div>";
}

echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "</body></html>";
?>