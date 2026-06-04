<?php
/**
 * Delete Category
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token bảo mật không hợp lệ.');
        redirect(baseUrl('admin/categories/index.php'));
    }

    $id = (int)($_POST['id'] ?? 0);
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE category_id = ?");
    $stmt->execute([$id]);

    if ($stmt->fetchColumn() > 0) {
        // Sản phẩm thuộc danh mục sẽ bị SET NULL do FK constraint
        $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Đã xóa danh mục thành công.');
    } else {
        setFlash('error', 'Danh mục không tồn tại.');
    }
}

redirect(baseUrl('admin/categories/index.php'));
