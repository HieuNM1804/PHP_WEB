<?php
/**
 * Delete Supplier
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token bảo mật không hợp lệ.');
        redirect(baseUrl('admin/suppliers/index.php'));
    }

    $id = (int)($_POST['id'] ?? 0);
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$id]);

    if ($stmt->fetchColumn() > 0) {
        // Sản phẩm liên kết sẽ bị SET NULL do FK constraint
        $stmt = $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Đã xóa nhà cung cấp thành công.');
    } else {
        setFlash('error', 'Nhà cung cấp không tồn tại.');
    }
}

redirect(baseUrl('admin/suppliers/index.php'));
