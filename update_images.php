<?php
/**
 * Update product images — Gán đường dẫn ảnh từ thư mục uploads vào DB
 */
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();

// Mapping category_id → folder name
$categoryFolders = [
    1 => 'ao-thun',
    2 => 'ao-so-mi',
    3 => 'quan-jeans',
    4 => 'dam',
    5 => 'ao-khoac',
    6 => 'phu-kien',
];

$baseDir = __DIR__ . '/assets/uploads/images/';

$products = $pdo->query("SELECT product_id, product_name, category_id FROM products ORDER BY product_id")->fetchAll();

$updated = 0;
$notFound = [];

foreach ($products as $p) {
    $catId = $p['category_id'];
    $pid = $p['product_id'];
    
    if (!isset($categoryFolders[$catId])) {
        $notFound[] = "#{$pid} {$p['product_name']} — không có folder cho category_id={$catId}";
        continue;
    }
    
    $folder = $categoryFolders[$catId];
    $imagePath = "images/{$folder}/{$pid}.jpg";
    $fullPath = $baseDir . "{$folder}/{$pid}.jpg";
    
    if (file_exists($fullPath)) {
        $stmt = $pdo->prepare("UPDATE products SET main_image = ? WHERE product_id = ?");
        $stmt->execute([$imagePath, $pid]);
        $updated++;
        echo "✓ #{$pid} {$p['product_name']} → {$imagePath}\n";
    } else {
        $notFound[] = "#{$pid} {$p['product_name']} — file không tồn tại: {$fullPath}";
    }
}

echo "\n=== Kết quả ===\n";
echo "Đã cập nhật: {$updated} sản phẩm\n";
if (!empty($notFound)) {
    echo "Không tìm thấy ảnh:\n";
    foreach ($notFound as $nf) echo "  - {$nf}\n";
}
echo "\nDone!\n";
