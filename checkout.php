<?php
require_once __DIR__ . '/includes/functions.php';

// Chặn admin truy cập trang web thường
blockAdminFromPublic();

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

// Xử lý đặt hàng
$order_success = false;
$order_id = null;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $cart_data = json_decode($_POST['cart_data'] ?? '[]', true);
    $shipping_info = [
        'name' => trim($_POST['shipping_name'] ?? ''),
        'phone' => trim($_POST['shipping_phone'] ?? ''),
        'address' => trim($_POST['shipping_address'] ?? ''),
        'note' => trim($_POST['order_note'] ?? '')
    ];
    
    // Validation
    if (empty($shipping_info['name']) || empty($shipping_info['phone']) || empty($shipping_info['address'])) {
        $error_message = 'Vui lòng điền đầy đủ thông tin giao hàng.';
    } elseif (empty($cart_data)) {
        $error_message = 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi đặt hàng.';
    } else {
        try {
            // Tạo bảng orders nếu chưa có
            $create_orders_table = "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_number VARCHAR(20) UNIQUE NOT NULL,
                total_amount DECIMAL(10,0) NOT NULL,
                shipping_name VARCHAR(100) NOT NULL,
                shipping_phone VARCHAR(20) NOT NULL,
                shipping_address TEXT NOT NULL,
                order_note TEXT,
                status ENUM('pending', 'confirmed', 'shipping', 'delivered', 'cancelled') DEFAULT 'pending',
                payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_orders (user_id),
                INDEX idx_order_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $mysqli->query($create_orders_table);
            
            // Tạo bảng order_items nếu chưa có
            $create_order_items_table = "CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                product_name VARCHAR(200) NOT NULL,
                product_price DECIMAL(10,0) NOT NULL,
                quantity INT NOT NULL,
                subtotal DECIMAL(10,0) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_order_items (order_id),
                INDEX idx_product_orders (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $mysqli->query($create_order_items_table);
            
            // Tính tổng tiền
            $total_amount = 0;
            foreach ($cart_data as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
            
            // Tạo mã đơn hàng
            $order_number = 'ORD' . date('Ymd') . sprintf('%04d', rand(1, 9999));
            
            // Bắt đầu transaction
            $mysqli->begin_transaction();
            
            // Tạo đơn hàng
            $order_stmt = $mysqli->prepare('INSERT INTO orders (user_id, order_number, total_amount, shipping_name, shipping_phone, shipping_address, order_note) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $order_stmt->bind_param('isisiss', $_SESSION['user_id'], $order_number, $total_amount, $shipping_info['name'], $shipping_info['phone'], $shipping_info['address'], $shipping_info['note']);
            
            if ($order_stmt->execute()) {
                $order_id = $mysqli->insert_id;
                
                // Thêm items vào đơn hàng
                $item_stmt = $mysqli->prepare('INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)');
                
                foreach ($cart_data as $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $item_stmt->bind_param('iisiii', $order_id, $item['id'], $item['name'], $item['price'], $item['quantity'], $subtotal);
                    $item_stmt->execute();
                    
                    // Cập nhật tồn kho
                    $update_stock = $mysqli->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?');
                    $update_stock->bind_param('iii', $item['quantity'], $item['id'], $item['quantity']);
                    $update_stock->execute();
                }
                
                $item_stmt->close();
                $mysqli->commit();
                $order_success = true;
                
            } else {
                $mysqli->rollback();
                $error_message = 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.';
            }
            
            $order_stmt->close();
            
        } catch (Exception $e) {
            $mysqli->rollback();
            $error_message = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Checkout Page -->
<section class="bg-primary text-white py-3">
    <div class="container">
        <h1 class="h4 mb-0">
            <i class="fas fa-shopping-cart me-2"></i>Thanh toán
        </h1>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <?php if ($order_success): ?>
            <!-- Order Success -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white text-center">
                            <h4 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>Đặt hàng thành công!
                            </h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-gift fa-3x text-success mb-3"></i>
                                <h5>Cảm ơn bạn đã đặt hàng!</h5>
                                <p class="text-muted">Mã đơn hàng của bạn là: <strong class="text-primary"><?php echo $order_number ?? ''; ?></strong></p>
                            </div>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng:</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Trạng thái:</strong> Đang xử lý</li>
                                    <li><strong>Thanh toán:</strong> Thanh toán khi nhận hàng (COD)</li>
                                    <li><strong>Giao hàng:</strong> 2-3 ngày làm việc</li>
                                </ul>
                            </div>
                            
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="order-history.php" class="btn btn-primary">
                                    <i class="fas fa-history me-2"></i>Xem đơn hàng
                                </a>
                                <a href="equipment.php" class="btn btn-outline-primary">
                                    <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Checkout Form -->
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" id="checkoutForm">
                <div class="row g-4">
                    <!-- Thông tin giao hàng -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-truck me-2"></i>Thông tin giao hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Họ tên *</label>
                                        <input type="text" name="shipping_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Số điện thoại *</label>
                                        <input type="tel" name="shipping_phone" class="form-control" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Địa chỉ giao hàng *</label>
                                        <textarea name="shipping_address" class="form-control" rows="3" 
                                                  placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Ghi chú đơn hàng</label>
                                        <textarea name="order_note" class="form-control" rows="2" 
                                                  placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Phương thức thanh toán -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Phương thức thanh toán
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        <i class="fas fa-money-bill-wave text-success me-2"></i>
                                        <strong>Thanh toán khi nhận hàng (COD)</strong>
                                        <small class="d-block text-muted">Thanh toán bằng tiền mặt khi nhận hàng</small>
                                    </label>
                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank" disabled>
                                    <label class="form-check-label text-muted" for="bank">
                                        <i class="fas fa-university text-muted me-2"></i>
                                        <strong>Chuyển khoản ngân hàng</strong>
                                        <small class="d-block text-muted">Sắp có (Coming soon)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tóm tắt đơn hàng -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>Tóm tắt đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="orderSummary">
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">Giỏ hàng trống</p>
                                        <a href="equipment.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
                                        </a>
                                    </div>
                                </div>
                                
                                <div id="orderTotal" style="display: none;">
                                    <hr>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tạm tính:</span>
                                        <span id="subtotal">0đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Phí vận chuyển:</span>
                                        <span class="text-success">Miễn phí</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Tổng cộng:</strong>
                                        <strong class="text-danger" id="totalAmount">0đ</strong>
                                    </div>
                                    
                                    <input type="hidden" name="cart_data" id="cartDataInput">
                                    
                                    <button type="submit" name="place_order" class="btn btn-danger w-100 btn-lg" id="placeOrderBtn" disabled>
                                        <i class="fas fa-check me-2"></i>Đặt hàng
                                    </button>
                                    
                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Thông tin của bạn được bảo mật
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chính sách -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6><i class="fas fa-info-circle text-primary me-2"></i>Chính sách mua hàng</h6>
                                <ul class="list-unstyled small text-muted mb-0">
                                    <li><i class="fas fa-check text-success me-2"></i>Miễn phí vận chuyển toàn quốc</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Đổi trả trong 7 ngày</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Bảo hành chính hãng</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Hỗ trợ 24/7</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load cart data from localStorage
    loadCartData();
    
    function loadCartData() {
        const cartData = JSON.parse(localStorage.getItem('cart') || '[]');
        const orderSummary = document.getElementById('orderSummary');
        const orderTotal = document.getElementById('orderTotal');
        const cartDataInput = document.getElementById('cartDataInput');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        
        if (cartData.length === 0) {
            orderSummary.style.display = 'block';
            orderTotal.style.display = 'none';
            placeOrderBtn.disabled = true;
            return;
        }
        
        // Hide empty cart message
        orderSummary.style.display = 'none';
        orderTotal.style.display = 'block';
        placeOrderBtn.disabled = false;
        
        // Generate order summary HTML
        let summaryHTML = '';
        let subtotal = 0;
        
        cartData.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            
            summaryHTML += `
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <img src="${item.image}" alt="${item.name}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 small">${item.name}</h6>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">SL: ${item.quantity}</span>
                            <span class="fw-bold small">${itemTotal.toLocaleString()}đ</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        orderSummary.innerHTML = summaryHTML;
        
        // Update totals
        document.getElementById('subtotal').textContent = subtotal.toLocaleString() + 'đ';
        document.getElementById('totalAmount').textContent = subtotal.toLocaleString() + 'đ';
        
        // Set cart data for form submission
        cartDataInput.value = JSON.stringify(cartData);
    }
    
    // Form validation
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const cartData = JSON.parse(localStorage.getItem('cart') || '[]');
            if (cartData.length === 0) {
                e.preventDefault();
                alert('Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
                return;
            }
            
            // Add loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Đang xử lý...';
            submitBtn.disabled = true;
        });
    }
    
    // Clear cart after successful order
    <?php if ($order_success): ?>
        localStorage.removeItem('cart');
        // Update cart count in header if exists
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            cartCount.textContent = '0';
        }
    <?php endif; ?>
});
</script>

<style>
.card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px 10px 0 0;
    border-bottom: 1px solid #dee2e6;
}

.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
    transform: translateY(-1px);
}

.form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}

.alert {
    border-radius: 8px;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
}
</style>