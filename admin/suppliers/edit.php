<?php
/**
 * Edit Supplier — Sửa nhà cung cấp
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

$pdo = getDBConnection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
$stmt->execute([$id]);
$supplier = $stmt->fetch();

if (!$supplier) {
    setFlash('error', 'Nhà cung cấp không tồn tại.');
    redirect(baseUrl('admin/suppliers/index.php'));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token bảo mật không hợp lệ.';
    }

    $name    = trim($_POST['supplier_name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status  = $_POST['status'] ?? 'Active';

    if (empty($name)) $errors[] = 'Tên nhà cung cấp không được để trống.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE suppliers SET supplier_name=?, phone=?, email=?, address=?, status=? WHERE supplier_id=?");
        $stmt->execute([$name, $phone ?: null, $email ?: null, $address ?: null, $status, $id]);
        setFlash('success', 'Cập nhật nhà cung cấp thành công!');
        redirect(baseUrl('admin/suppliers/index.php'));
    }
}

$pageTitle = 'Sửa nhà cung cấp';
$currentPage = 'suppliers';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= baseUrl('admin/suppliers/index.php') ?>" class="btn btn-secondary btn-sm">
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
            <div class="form-group full-width">
                <label for="supplier_name">Tên nhà cung cấp <span class="required">*</span></label>
                <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="<?= e($supplier['supplier_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Điện thoại</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= e($supplier['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($supplier['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" id="address" name="address" class="form-control" value="<?= e($supplier['address'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="Active" <?= ($supplier['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($supplier['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Cập nhật
            </button>
            <a href="<?= baseUrl('admin/suppliers/index.php') ?>" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
