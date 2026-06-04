<?php
/**
 * Delete Product
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Token bảo mật không hợp lệ.');
        redirect(baseUrl('admin/products/index.php'));
    }

    $id = (int)($_POST['id'] ?? 0);
    $pdo = getDBConnection();

    // Lấy info ảnh để xóa file
    $stmt = $pdo->prepare("SELECT main_image FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        if ($product['main_image']) {
            $path = __DIR__ . '/../../assets/uploads/' . $product['main_image'];
            if (file_exists($path)) unlink($path);
        }
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Đã xóa sản phẩm thành công.');
    } else {
        setFlash('error', 'Sản phẩm không tồn tại.');
    }
}

redirect(baseUrl('admin/products/index.php'));
