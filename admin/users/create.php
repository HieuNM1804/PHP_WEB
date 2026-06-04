<?php
/**
 * Create User — Thêm người dùng mới
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

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'Staff';
    $status   = $_POST['status'] ?? 'Active';

    if (empty($username)) $errors[] = 'Tên đăng nhập không được để trống.';
    if (strlen($username) < 3) $errors[] = 'Tên đăng nhập tối thiểu 3 ký tự.';
    if (empty($password)) $errors[] = 'Mật khẩu không được để trống.';
    if (strlen($password) < 6) $errors[] = 'Mật khẩu tối thiểu 6 ký tự.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';

    if (empty($errors)) {
        $pdo = getDBConnection();

        // Kiểm tra trùng username
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Tên đăng nhập đã tồn tại.';
        }

        // Kiểm tra trùng email
        if ($email) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Email đã được sử dụng.';
            }
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, email, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $hash, $role, $email ?: null, $status]);
        setFlash('success', 'Thêm người dùng thành công!');
        redirect(baseUrl('admin/users/index.php'));
    }
}

$pageTitle = 'Thêm người dùng';
$currentPage = 'users';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= baseUrl('admin/users/index.php') ?>" class="btn btn-secondary btn-sm">
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
                <label for="username">Tên đăng nhập <span class="required">*</span></label>
                <input type="text" id="username" name="username" class="form-control" value="<?= e($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" required minlength="6">
            </div>

            <div class="form-group">
                <label for="role">Vai trò</label>
                <select id="role" name="role" class="form-control">
                    <option value="Admin" <?= (($_POST['role'] ?? '') === 'Admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="Staff" <?= (($_POST['role'] ?? 'Staff') === 'Staff') ? 'selected' : '' ?>>Staff</option>
                    <option value="Customer" <?= (($_POST['role'] ?? '') === 'Customer') ? 'selected' : '' ?>>Customer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Lưu người dùng
            </button>
            <a href="<?= baseUrl('admin/users/index.php') ?>" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
