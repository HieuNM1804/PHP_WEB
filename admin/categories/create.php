<?php
/**
 * Create Category — Thêm danh mục mới
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token bảo mật không hợp lệ.';
    }

    $name   = trim($_POST['category_name'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'Active';

    if (empty($name)) $errors[] = 'Tên danh mục không được để trống.';

    if (empty($errors)) {
        $pdo = getDBConnection();

        // Kiểm tra trùng tên
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE category_name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Tên danh mục đã tồn tại.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO categories (category_name, description, status) VALUES (?, ?, ?)");
        $stmt->execute([$name, $desc ?: null, $status]);
        setFlash('success', 'Thêm danh mục thành công!');
        redirect(baseUrl('admin/categories/index.php'));
    }
}

$pageTitle = 'Thêm danh mục';
$currentPage = 'categories';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= baseUrl('admin/categories/index.php') ?>" class="btn btn-secondary btn-sm">
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
    <form method="POST">
        <?= csrfField() ?>
        <div class="form-grid">
            <div class="form-group">
                <label for="category_name">Tên danh mục <span class="required">*</span></label>
                <input type="text" id="category_name" name="category_name" class="form-control" value="<?= e($_POST['category_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= e($_POST['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Lưu danh mục
            </button>
            <a href="<?= baseUrl('admin/categories/index.php') ?>" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
