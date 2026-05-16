<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$isAdminPage = true;
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $mysqli->prepare('DELETE FROM courts WHERE id = ?');
        $stmt->bind_param('i', $delete_id);
        $stmt->execute();
        $message = 'Đã xóa sân.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $price = intval($_POST['price'] ?? 0);
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($name && $location && $price) {
            if (!empty($_POST['edit_id'])) {
                $edit_id = intval($_POST['edit_id']);
                $stmt = $mysqli->prepare('UPDATE courts SET name = ?, location = ?, price_per_hour = ?, cover_image = ?, description = ? WHERE id = ?');
                $stmt->bind_param('ssissi', $name, $location, $price, $image, $description, $edit_id);
                $stmt->execute();
                $message = 'Cập nhật sân thành công.';
            } else {
                $stmt = $mysqli->prepare('INSERT INTO courts (name, location, price_per_hour, cover_image, description) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('ssiss', $name, $location, $price, $image, $description);
                $stmt->execute();
                $message = 'Thêm sân mới thành công.';
            }
        }
    }
}
$courts = getAllCourts();
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="card-title">Quản lý sân</h2>
                    <p class="text-muted mb-0">Thêm, sửa hoặc xóa sân.</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline-secondary">Bảng điều khiển</a>
            </div>
        </div>
    </div>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo escape($message); ?></div>
<?php endif; ?>
<div class="row gy-4">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Thêm / sửa sân</h4>
                <form method="post">
                    <input type="hidden" name="edit_id" id="edit_id" value="">
                    <div class="mb-3">
                        <label class="form-label">Tên sân</label>
                        <input type="text" name="name" id="court_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phường / Xã (Hà Nội)</label>
                        <input list="hanoiWards" type="text" name="location" id="court_location" class="form-control" placeholder="Ví dụ: Trung Hòa, Trúc Bạch, Dịch Vọng" required>
                        <datalist id="hanoiWards">
                            <option value="Trung Hòa"></option>
                            <option value="Trúc Bạch"></option>
                            <option value="Dịch Vọng"></option>
                            <option value="Nguyễn Du"></option>
                            <option value="Hàng Bông"></option>
                            <option value="Nhật Tân"></option>
                            <option value="Văn Quán"></option>
                            <option value="Bồ Đề"></option>
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá mỗi giờ</label>
                        <input type="number" name="price" id="court_price" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh bìa</label>
                        <input type="text" name="image" id="court_image" class="form-control" placeholder="URL ảnh">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="court_description" class="form-control" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lưu sân</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">Danh sách sân</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên sân</th>
                                <th>Khu vực</th>
                                <th>Giá</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courts as $court): ?>
                                <tr>
                                    <td><?php echo escape($court['id']); ?></td>
                                    <td><?php echo escape($court['name']); ?></td>
                                    <td><?php echo escape($court['location']); ?></td>
                                    <td><?php echo number_format($court['price_per_hour']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary editCourtBtn" 
                                            data-id="<?php echo escape($court['id']); ?>"
                                            data-name="<?php echo escape($court['name']); ?>"
                                            data-location="<?php echo escape($court['location']); ?>"
                                            data-price="<?php echo escape($court['price_per_hour']); ?>"
                                            data-image="<?php echo escape($court['cover_image']); ?>"
                                            data-description="<?php echo escape($court['description']); ?>">
                                            Sửa
                                        </button>
                                        <form method="post" class="d-inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa sân này?');">
                                            <input type="hidden" name="delete_id" value="<?php echo escape($court['id']); ?>">
                                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script>
document.querySelectorAll('.editCourtBtn').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('edit_id').value = button.dataset.id;
        document.getElementById('court_name').value = button.dataset.name;
        document.getElementById('court_location').value = button.dataset.location;
        document.getElementById('court_price').value = button.dataset.price;
        document.getElementById('court_image').value = button.dataset.image;
        document.getElementById('court_description').value = button.dataset.description;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>
