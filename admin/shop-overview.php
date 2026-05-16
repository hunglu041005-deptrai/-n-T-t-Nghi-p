<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$isAdminPage = true;

// Get shop statistics
$stats = [];
try {
    $stats['total_products'] = $mysqli->query('SELECT COUNT(*) as count FROM products')->fetch_assoc()['count'];
    $stats['active_products'] = $mysqli->query('SELECT COUNT(*) as count FROM products WHERE status = 1')->fetch_assoc()['count'];
    $stats['total_categories'] = $mysqli->query('SELECT COUNT(*) as count FROM product_categories')->fetch_assoc()['count'];
    $stats['active_categories'] = $mysqli->query('SELECT COUNT(*) as count FROM product_categories WHERE status = 1')->fetch_assoc()['count'];
    $stats['low_stock'] = $mysqli->query('SELECT COUNT(*) as count FROM products WHERE stock_quantity <= 5 AND status = 1')->fetch_assoc()['count'];
    $stats['featured_products'] = $mysqli->query('SELECT COUNT(*) as count FROM products WHERE featured = 1 AND status = 1')->fetch_assoc()['count'];
    $stats['out_of_stock'] = $mysqli->query('SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0 AND status = 1')->fetch_assoc()['count'];
    
    // Recent activity
    $recent_products = $mysqli->query('SELECT name, created_at FROM products ORDER BY created_at DESC LIMIT 5')->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $stats = array_fill_keys(['total_products', 'active_products', 'total_categories', 'active_categories', 'low_stock', 'featured_products', 'out_of_stock'], 0);
    $recent_products = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white py-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold mb-3">
                                <i class="fas fa-chart-line me-3"></i>Tổng quan Shop
                            </h1>
                            <p class="lead mb-0 opacity-90">
                                Thống kê và quản lý hệ thống shop
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-none d-md-block">
                                <i class="fas fa-store" style="font-size: 4rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-bolt text-warning me-2"></i>Thao tác nhanh
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <a href="shop.php" class="btn btn-primary w-100">
                                <i class="fas fa-cogs me-2"></i>Quản lý Shop
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="../equipment.php" target="_blank" class="btn btn-success w-100">
                                <i class="fas fa-external-link-alt me-2"></i>Xem trang khách
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="../test-shop-sync.php" target="_blank" class="btn btn-info w-100">
                                <i class="fas fa-sync me-2"></i>Test Sync
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="../add-sample-products.php" class="btn btn-warning w-100">
                                <i class="fas fa-plus me-2"></i>Thêm mẫu
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="setup-shop.php" class="btn btn-secondary w-100">
                                <i class="fas fa-database me-2"></i>Setup DB
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="users.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-users me-2"></i>Người dùng
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="reviews.php" class="btn btn-outline-warning w-100">
                                <i class="fas fa-star me-2"></i>Đánh giá
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #007bff !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-box text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted text-uppercase mb-1">Tổng sản phẩm</h6>
                            <h3 class="mb-0"><?php echo $stats['total_products']; ?></h3>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                <?php echo $stats['active_products']; ?> đang hoạt động
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-tags text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted text-uppercase mb-1">Danh mục</h6>
                            <h3 class="mb-0"><?php echo $stats['total_categories']; ?></h3>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                <?php echo $stats['active_categories']; ?> đang hoạt động
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted text-uppercase mb-1">Sắp hết hàng</h6>
                            <h3 class="mb-0"><?php echo $stats['low_stock']; ?></h3>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                ≤ 5 sản phẩm
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-star text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted text-uppercase mb-1">Sản phẩm nổi bật</h6>
                            <h3 class="mb-0"><?php echo $stats['featured_products']; ?></h3>
                            <small class="text-info">
                                <i class="fas fa-star me-1"></i>
                                Đang hiển thị
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Products -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>Sản phẩm mới nhất
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_products)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_products as $product): ?>
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-box text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($product['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có sản phẩm nào</p>
                            <a href="../add-sample-products.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm sản phẩm mẫu
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-server text-success me-2"></i>Trạng thái hệ thống
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h6 class="mb-1">Database Connection</h6>
                                    <small class="text-muted">Kết nối cơ sở dữ liệu</small>
                                </div>
                                <div>
                                    <?php if ($mysqli->ping()): ?>
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check me-1"></i>Kết nối OK
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times me-1"></i>Lỗi kết nối
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h6 class="mb-1">Shop Tables</h6>
                                    <small class="text-muted">Bảng dữ liệu shop</small>
                                </div>
                                <div>
                                    <?php
                                    $tables_exist = true;
                                    $required_tables = ['product_categories', 'products'];
                                    foreach ($required_tables as $table) {
                                        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
                                        if (!$result || $result->num_rows == 0) {
                                            $tables_exist = false;
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($tables_exist): ?>
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check me-1"></i>Đã tạo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times me-1"></i>Chưa tạo
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h6 class="mb-1">Sync Status</h6>
                                    <small class="text-muted">Đồng bộ admin ↔ khách hàng</small>
                                </div>
                                <div>
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="fas fa-sync me-1"></i>Real-time
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h6 class="mb-1">Last Update</h6>
                                    <small class="text-muted">Cập nhật cuối cùng</small>
                                </div>
                                <div>
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="fas fa-clock me-1"></i><?php echo date('H:i:s'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Instructions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check text-info me-2"></i>Hướng dẫn Test hệ thống
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">🔄 Test Sync (Đồng bộ):</h6>
                            <ol class="small">
                                <li>Mở <strong>Admin Shop</strong> và <strong>Trang khách hàng</strong> trong 2 tab</li>
                                <li><strong>Thêm sản phẩm mới</strong> → Refresh trang khách → Sản phẩm xuất hiện</li>
                                <li><strong>Sửa sản phẩm</strong> (tên, giá) → Refresh → Thông tin thay đổi</li>
                                <li><strong>Xóa sản phẩm</strong> → Refresh → Sản phẩm biến mất</li>
                                <li><strong>Cập nhật tồn kho</strong> → Refresh → Trạng thái hàng thay đổi</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">✅ Chức năng Admin:</h6>
                            <ul class="small">
                                <li>✏️ <strong>Sửa danh mục:</strong> Click nút sửa → Form tự động điền</li>
                                <li>🗑️ <strong>Xóa danh mục:</strong> Chỉ xóa khi không có sản phẩm</li>
                                <li>✏️ <strong>Sửa sản phẩm:</strong> Click sửa → Chuyển tab → Form điền sẵn</li>
                                <li>🗑️ <strong>Xóa sản phẩm:</strong> Xác nhận trước khi xóa</li>
                                <li>📦 <strong>Cập nhật tồn kho:</strong> Thay đổi số lượng trực tiếp</li>
                                <li>🛒 <strong>Giỏ hàng:</strong> Test thêm sản phẩm vào giỏ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
}

.btn {
    border-radius: 10px;
}

.badge {
    border-radius: 8px;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>