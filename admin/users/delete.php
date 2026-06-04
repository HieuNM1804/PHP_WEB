<?php
/**
 * Delete User
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token bảo mật không hợp lệ.');
        redirect(baseUrl('admin/users/index.php'));
    }

    $id = (int)($_POST['id'] ?? 0);

    // Không cho xóa chính mình
    if ($id === (int)$_SESSION['user_id']) {
        setFlash('error', 'Không thể xóa tài khoản đang đăng nhập.');
        redirect(baseUrl('admin/users/index.php'));
    }

    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
    $stmt->execute([$id]);

    if ($stmt->fetchColumn() > 0) {
        // Xóa customer liên kết trước (nếu có)
        $pdo->prepare("DELETE FROM customers WHERE user_id = ?")->execute([$id]);
        // Xóa user
        $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$id]);
        setFlash('success', 'Đã xóa người dùng thành công.');
    } else {
        setFlash('error', 'Người dùng không tồn tại.');
    }
}

redirect(baseUrl('admin/users/index.php'));
