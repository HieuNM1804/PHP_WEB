<?php
/**
 * Create Product — Thêm sản phẩm mới
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

$pdo = getDBConnection();
$categories = $pdo->query("SELECT * FROM categories WHERE status='Active' ORDER BY category_name")->fetchAll();
$suppliers  = $pdo->query("SELECT * FROM suppliers WHERE status='Active' ORDER BY supplier_name")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token bảo mật không hợp lệ.';
    }

    $name     = trim($_POST['product_name'] ?? '');
    $catId    = (int)($_POST['category_id'] ?? 0);
    $supId    = (int)($_POST['supplier_id'] ?? 0);
    $price    = (float)($_POST['unit_price'] ?? 0);
    $oldPrice = ($_POST['old_price'] !== '') ? (float)$_POST['old_price'] : null;
    $stock    = (int)($_POST['stock_quantity'] ?? 0);
    $desc     = trim($_POST['description'] ?? '');
    $status   = $_POST['status'] ?? 'Active';

    if (empty($name)) $errors[] = 'Tên sản phẩm không được để trống.';
    if ($price <= 0)   $errors[] = 'Giá phải lớn hơn 0.';

    // Upload image
    $imageName = null;
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($_FILES['main_image']['type'], $allowed)) {
            $errors[] = 'Chỉ cho phép file ảnh (JPG, PNG, WebP, GIF).';
        } else {
            $ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
            $imageName = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../assets/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadDir . $imageName);
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (product_name, category_id, supplier_id, unit_price, old_price, stock_quantity, main_image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $name,
            $catId ?: null,
            $supId ?: null,
            $price,
            $oldPrice,
            $stock,
            $imageName,
            $desc,
            $status
        ]);
        setFlash('success', 'Thêm sản phẩm thành công!');
        redirect(baseUrl('admin/products/index.php'));
    }
}

$pageTitle = 'Thêm sản phẩm';
$currentPage = 'products';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= baseUrl('admin/products/index.php') ?>" class="btn btn-secondary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Quay lại
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <span><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></span>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="product_name">Tên sản phẩm <span class="required">*</span></label>
                <input type="text" id="product_name" name="product_name" class="form-control" value="<?= e($_POST['product_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Danh mục</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="0">— Chọn danh mục —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= (($_POST['category_id'] ?? '') == $cat['category_id']) ? 'selected' : '' ?>><?= e($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="supplier_id">Nhà cung cấp</label>
                <select id="supplier_id" name="supplier_id" class="form-control">
                    <option value="0">— Chọn NCC —</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['supplier_id'] ?>" <?= (($_POST['supplier_id'] ?? '') == $sup['supplier_id']) ? 'selected' : '' ?>><?= e($sup['supplier_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="unit_price">Giá bán <span class="required">*</span></label>
                <input type="number" id="unit_price" name="unit_price" class="form-control" step="1000" min="0" value="<?= e($_POST['unit_price'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="old_price">Giá cũ (nếu có)</label>
                <input type="number" id="old_price" name="old_price" class="form-control" step="1000" min="0" value="<?= e($_POST['old_price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="stock_quantity">Tồn kho</label>
                <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" min="0" value="<?= e($_POST['stock_quantity'] ?? '0') ?>">
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="Active" <?= (($_POST['status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= (($_POST['status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="main_image">Hình ảnh</label>
                <input type="file" id="main_image" name="main_image" class="form-control" accept="image/*">
                <img id="imagePreview" style="display:none; margin-top:10px; max-height:150px; border-radius:8px;" alt="Preview">
            </div>

            <div class="form-group full-width">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= e($_POST['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Lưu sản phẩm
            </button>
            <a href="<?= baseUrl('admin/products/index.php') ?>" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
