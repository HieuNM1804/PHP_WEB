<?php
/**
 * Dashboard — Trang chính admin với thống kê 
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();

// === Thống kê tổng quan ===
$totalProducts   = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers      = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalSuppliers  = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();


// === Nhóm 2.2: Thống kê theo danh mục ===
$categoryStats = $pdo->query("
    SELECT c.category_name, COUNT(p.product_id) AS product_count
    FROM categories c
    LEFT JOIN products p ON c.category_id = p.category_id
    WHERE c.status = 'Active'
    GROUP BY c.category_id, c.category_name
    ORDER BY product_count DESC
")->fetchAll();

$maxCategoryCount = 0;
foreach ($categoryStats as $cs) {
    if ($cs['product_count'] > $maxCategoryCount) $maxCategoryCount = $cs['product_count'];
}

// === Nhóm 2.3: Thống kê theo khoảng giá ===
$priceRanges = [
    ['label' => 'Dưới 300.000₫',       'min' => 0,       'max' => 299999],
    ['label' => '300.000₫ — 500.000₫',  'min' => 300000,  'max' => 500000],
    ['label' => '500.000₫ — 1.000.000₫','min' => 500001,  'max' => 1000000],
    ['label' => 'Trên 1.000.000₫',      'min' => 1000001, 'max' => 999999999],
];

$priceStats = [];
$maxPriceCount = 0;
foreach ($priceRanges as $range) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE unit_price >= ? AND unit_price <= ? AND status = 'Active'");
    $stmt->execute([$range['min'], $range['max']]);
    $count = $stmt->fetchColumn();
    $priceStats[] = ['label' => $range['label'], 'count' => $count];
    if ($count > $maxPriceCount) $maxPriceCount = $count;
}

// === Dữ liệu gần đây ===
$latestProducts = $pdo->query("
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id 
    ORDER BY p.created_at DESC LIMIT 5
")->fetchAll();

$latestUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

$barColors = ['purple', 'blue', 'green', 'orange', 'pink', 'teal'];

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        </div>
        <div class="stat-info">
            <h3><?= $totalProducts ?></h3>
            <p>Sản phẩm</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-info">
            <h3><?= $totalUsers ?></h3>
            <p>Người dùng</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </div>
        <div class="stat-info">
            <h3><?= $totalCategories ?></h3>
            <p>Danh mục</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="stat-info">
            <h3><?= $totalSuppliers ?></h3>
            <p>Nhà cung cấp</p>
        </div>
    </div>
</div>



<!-- Category & Price Stats Side by Side -->
<div class="dashboard-grid">
    <!-- Category Stats -->
    <div class="dashboard-section" style="margin-bottom:0">
        <div class="dashboard-section-header">
            <h2>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Sản phẩm theo danh mục
            </h2>
        </div>
        <div class="dashboard-section-body">
            <div class="stats-bars">
                <?php foreach ($categoryStats as $i => $cs): ?>
                <div class="stats-bar-item">
                    <span class="stats-bar-label"><?= e($cs['category_name']) ?></span>
                    <div class="stats-bar-track">
                        <div class="stats-bar-fill <?= $barColors[$i % count($barColors)] ?>" style="width: <?= $maxCategoryCount > 0 ? round($cs['product_count'] / $maxCategoryCount * 100) : 0 ?>%">
                            <?= $cs['product_count'] ?>
                        </div>
                    </div>
                    <span class="stats-bar-count"><?= $cs['product_count'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Price Range Stats -->
    <div class="dashboard-section" style="margin-bottom:0">
        <div class="dashboard-section-header">
            <h2>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Phân bố theo khoảng giá
            </h2>
        </div>
        <div class="dashboard-section-body">
            <div class="stats-bars">
                <?php 
                $priceColors = ['green', 'blue', 'orange', 'pink'];
                foreach ($priceStats as $i => $ps): 
                ?>
                <div class="stats-bar-item">
                    <span class="stats-bar-label"><?= $ps['label'] ?></span>
                    <div class="stats-bar-track">
                        <div class="stats-bar-fill <?= $priceColors[$i % count($priceColors)] ?>" style="width: <?= $maxPriceCount > 0 ? round($ps['count'] / $maxPriceCount * 100) : 0 ?>%">
                            <?= $ps['count'] ?>
                        </div>
                    </div>
                    <span class="stats-bar-count"><?= $ps['count'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Latest Products -->
<div class="table-wrapper" style="margin-bottom: 28px;">
    <div class="table-header">
        <h2>Sản phẩm mới nhất</h2>
        <a href="<?= baseUrl('admin/products/index.php') ?>" class="btn btn-secondary btn-sm">Xem tất cả</a>
    </div>
    <table>
        <thead><tr>
            <th>Sản phẩm</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Trạng thái</th>
        </tr></thead>
        <tbody>
        <?php foreach ($latestProducts as $p): ?>
        <tr>
            <td>
                <div class="product-cell">
                    <?php if ($p['main_image'] && file_exists(__DIR__ . '/../assets/uploads/' . $p['main_image'])): ?>
                        <img src="<?= baseUrl('assets/uploads/' . $p['main_image']) ?>" class="product-thumb" alt="">
                    <?php else: ?>
                        <div class="product-thumb-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    <?php endif; ?>
                    <span><?= e($p['product_name']) ?></span>
                </div>
            </td>
            <td><?= e($p['category_name'] ?? '—') ?></td>
            <td><?= formatPrice($p['unit_price']) ?></td>
            <td>
                <?php if ($p['stock_quantity'] == 0): ?>
                    <span class="badge badge-danger"><?= $p['stock_quantity'] ?></span>
                <?php elseif ($p['stock_quantity'] < 10): ?>
                    <span class="badge badge-warning"><?= $p['stock_quantity'] ?></span>
                <?php else: ?>
                    <?= $p['stock_quantity'] ?>
                <?php endif; ?>
            </td>
            <td><span class="badge <?= $p['status'] === 'Active' ? 'badge-success' : 'badge-danger' ?>"><?= $p['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Latest Users -->
<div class="table-wrapper">
    <div class="table-header">
        <h2>Người dùng mới nhất</h2>
        <a href="<?= baseUrl('admin/users/index.php') ?>" class="btn btn-secondary btn-sm">Xem tất cả</a>
    </div>
    <table>
        <thead><tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
        </tr></thead>
        <tbody>
        <?php foreach ($latestUsers as $u): ?>
        <tr>
            <td><strong><?= e($u['username']) ?></strong></td>
            <td><?= e($u['email'] ?? '—') ?></td>
            <td><span class="badge badge-info"><?= e($u['role']) ?></span></td>
            <td><span class="badge <?= $u['status'] === 'Active' ? 'badge-success' : 'badge-danger' ?>"><?= $u['status'] ?></span></td>
            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
