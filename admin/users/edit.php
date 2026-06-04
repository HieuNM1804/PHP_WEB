<?php
/**
 * Edit User — Sửa thông tin người dùng
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('error', 'Người dùng không tồn tại.');
    redirect(baseUrl('admin/users/index.php'));
}

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
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';

    // Kiểm tra trùng username (trừ user hiện tại)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND user_id != ?");
        $stmt->execute([$username, $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Tên đăng nhập đã tồn tại.';
        }
    }

    // Kiểm tra trùng email
    if ($email && empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email đã được sử dụng.';
        }
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, password_hash=?, role=?, email=?, status=? WHERE user_id=?");
            $stmt->execute([$username, $hash, $role, $email ?: null, $status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, role=?, email=?, status=? WHERE user_id=?");
            $stmt->execute([$username, $role, $email ?: null, $status, $id]);
        }

        // Cập nhật session nếu sửa chính mình
        if ($id === (int)$_SESSION['user_id']) {
            $_SESSION['username'] = $username;
            $_SESSION['role']     = $role;
            $_SESSION['email']    = $email;
        }

        setFlash('success', 'Cập nhật người dùng thành công!');
        redirect(baseUrl('admin/users/index.php'));
    }
}

$pageTitle = 'Sửa người dùng';
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
                <input type="text" id="username" name="username" class="form-control" value="<?= e($user['username']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu mới <small>(bỏ trống nếu không đổi)</small></label>
                <input type="password" id="password" name="password" class="form-control" minlength="6">
            </div>

            <div class="form-group">
                <label for="role">Vai trò</label>
                <select id="role" name="role" class="form-control">
                    <option value="Admin" <?= ($user['role'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="Staff" <?= ($user['role'] === 'Staff') ? 'selected' : '' ?>>Staff</option>
                    <option value="Customer" <?= ($user['role'] === 'Customer') ? 'selected' : '' ?>>Customer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="Active" <?= ($user['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($user['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Cập nhật
            </button>
            <a href="<?= baseUrl('admin/users/index.php') ?>" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
