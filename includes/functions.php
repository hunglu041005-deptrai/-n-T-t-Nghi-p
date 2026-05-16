<?php
require_once __DIR__ . '/../db.php';

function getCourts($filters = []) {
    global $mysqli;
    $sql = 'SELECT * FROM courts WHERE status = 1';
    $params = [];
    $types = '';

    if (!empty($filters['q'])) {
        $sql .= ' AND (name LIKE ? OR description LIKE ? OR location LIKE ?)';
        $value = '%' . $filters['q'] . '%';
        $params[] = &$value;
        $params[] = &$value;
        $params[] = &$value;
        $types .= 'sss';
    }
    if (!empty($filters['location'])) {
        $sql .= ' AND location LIKE ?';
        $value = '%' . $filters['location'] . '%';
        $params[] = &$value;
        $types .= 's';
    }
    if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
        $sql .= ' AND price_per_hour >= ?';
        $params[] = &$filters['min_price'];
        $types .= 'i';
    }
    if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
        $sql .= ' AND price_per_hour <= ?';
        $params[] = &$filters['max_price'];
        $types .= 'i';
    }
    if (empty($filters['min_price']) && empty($filters['max_price']) && !empty($filters['price'])) {
        if ($filters['price'] === 'low') {
            $sql .= ' AND price_per_hour <= 100000';
        } elseif ($filters['price'] === 'mid') {
            $sql .= ' AND price_per_hour BETWEEN 100001 AND 150000';
        } elseif ($filters['price'] === 'high') {
            $sql .= ' AND price_per_hour > 150000';
        }
    }

    $sort = $filters['sort'] ?? '';
    if ($sort === 'price_asc') {
        $sql .= ' ORDER BY price_per_hour ASC';
    } elseif ($sort === 'price_desc') {
        $sql .= ' ORDER BY price_per_hour DESC';
    } elseif ($sort === 'newest') {
        $sql .= ' ORDER BY created_at DESC';
    } else {
        $sql .= ' ORDER BY created_at DESC';
    }

    $stmt = $mysqli->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $courts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $courts;
}

function getCourtById($id) {
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT * FROM courts WHERE id = ? AND status = 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $court = $result->fetch_assoc();
    $stmt->close();
    return $court;
}

function getCourtAvailability($court_id, $date) {
    global $mysqli;
    $sql = 'SELECT start_time, end_time FROM bookings WHERE court_id = ? AND booking_date = ? AND status != "cancelled"';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('is', $court_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }
    $stmt->close();
    return $slots;
}

function isSlotAvailable($court_id, $date, $start, $end) {
    global $mysqli;
    $sql = 'SELECT COUNT(*) AS count FROM bookings WHERE court_id = ? AND booking_date = ? AND status != "cancelled" AND NOT (end_time <= ? OR start_time >= ?)';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('isss', $court_id, $date, $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['count'] == 0;
}

function getUserBookings($user_id) {
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT b.*, c.name AS court_name, c.location FROM bookings b JOIN courts c ON b.court_id = c.id WHERE b.user_id = ? ORDER BY b.booking_date DESC, b.start_time DESC');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $bookings;
}

function getLocations() {
    global $mysqli;
    $result = $mysqli->query('SELECT DISTINCT location FROM courts WHERE status = 1 AND location != "" ORDER BY location ASC');
    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['location'];
    }
    return $locations;
}

function getBookingCounts() {
    global $mysqli;
    $result = $mysqli->query('SELECT status, COUNT(*) AS total FROM bookings GROUP BY status');
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $counts[$row['status']] = $row['total'];
    }
    return $counts;
}

function getRevenueByMonth() {
    global $mysqli;
    $result = $mysqli->query('SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, SUM(total_price) AS revenue FROM bookings WHERE status = "confirmed" GROUP BY month ORDER BY month ASC');
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function getAllUsers() {
    global $mysqli;
    $result = $mysqli->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC');
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllBookings() {
    global $mysqli;
    $result = $mysqli->query('SELECT b.*, u.name AS user_name, u.email AS user_email, c.name AS court_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN courts c ON b.court_id = c.id ORDER BY b.booking_date DESC, b.start_time DESC');
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllCourts() {
    global $mysqli;
    $result = $mysqli->query('SELECT * FROM courts ORDER BY id DESC');
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCourtCount() {
    global $mysqli;
    $result = $mysqli->query('SELECT COUNT(*) AS total FROM courts WHERE status = 1');
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

function getRecentBookings($limit = 5) {
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT b.id, b.booking_date, b.start_time, b.end_time, b.total_price, b.status, u.name AS user_name, c.name AS court_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN courts c ON b.court_id = c.id ORDER BY b.created_at DESC LIMIT ?');
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $bookings;
}

function getBookingById($booking_id) {
    global $mysqli;
    $stmt = $mysqli->prepare('
        SELECT b.*, u.email as user_email, u.name as user_name, c.name as court_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN courts c ON b.court_id = c.id
        WHERE b.id = ?
    ');
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    return $booking;
}

// Thêm chức năng admin đơn giản
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../login.php');
        exit;
    }
}

function blockAdminFromPublic() {
    if (isAdmin()) {
        header('Location: admin-redirect.php');
        exit;
    }
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Shop functions
function getShopCategories() {
    global $mysqli;
    $result = $mysqli->query('SELECT * FROM product_categories WHERE status = 1 ORDER BY sort_order, name');
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getShopProducts($category_id = null, $limit = null) {
    global $mysqli;
    $sql = 'SELECT p.*, c.name as category_name FROM products p LEFT JOIN product_categories c ON p.category_id = c.id WHERE p.status = 1';
    
    if ($category_id) {
        $sql .= ' AND p.category_id = ' . intval($category_id);
    }
    
    $sql .= ' ORDER BY p.featured DESC, p.created_at DESC';
    
    if ($limit) {
        $sql .= ' LIMIT ' . intval($limit);
    }
    
    $result = $mysqli->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getProductById($id) {
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT p.*, c.name as category_name FROM products p LEFT JOIN product_categories c ON p.category_id = c.id WHERE p.id = ? AND p.status = 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}

function getProductVariants($product_id) {
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT * FROM product_variants WHERE product_id = ? AND status = 1 ORDER BY type, value');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $variants = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $variants;
}

function getShopStats() {
    global $mysqli;
    $stats = [];
    
    $stats['total_products'] = $mysqli->query('SELECT COUNT(*) as count FROM products WHERE status = 1')->fetch_assoc()['count'];
    $stats['total_categories'] = $mysqli->query('SELECT COUNT(*) as count FROM product_categories WHERE status = 1')->fetch_assoc()['count'];
    $stats['low_stock'] = $mysqli->query('SELECT COUNT(*) as count FROM products WHERE stock_quantity <= 5 AND status = 1')->fetch_assoc()['count'];
    $stats['total_orders'] = $mysqli->query('SELECT COUNT(*) as count FROM orders')->fetch_assoc()['count'];
    
    return $stats;
}
?>