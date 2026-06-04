<?php
/**
 * Product Filter & Search Logic
 * Xử lý tìm kiếm, lọc, sắp xếp và phân trang sản phẩm
 * 
 * Yêu cầu: $pdo (PDO connection) phải được khởi tạo trước khi include file này.
 * 
 * Biến đầu ra:
 *   - $search, $sortBy, $sortDir, $page, $limit, $offset
 *   - $filterCat, $filterSup, $filterStatus, $priceMin, $priceMax
 *   - $products, $total, $totalPages
 *   - $categories, $suppliers
 *   - $hasFilters
 *   - Hàm: sortUrl(), sortClass(), pageUrl()
 */
/** @var PDO $pdo */
// === Search, Sort & Pagination ===
$search    = trim($_GET['search'] ?? '');
$sortBy    = $_GET['sort'] ?? 'created_at';
$sortDir   = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$page      = max(1, (int)($_GET['page'] ?? 1));
$limit     = 10;
$offset    = ($page - 1) * $limit;

// === Filters ===
$filterCat    = (int)($_GET['category'] ?? 0);
$filterSup    = (int)($_GET['supplier'] ?? 0);
$filterStatus = $_GET['status'] ?? '';
$priceMin     = ($_GET['price_min'] ?? '') !== '' ? (float)$_GET['price_min'] : null;
$priceMax     = ($_GET['price_max'] ?? '') !== '' ? (float)$_GET['price_max'] : null;

// === Whitelist sort columns ===
$allowedSort = ['product_name', 'unit_price', 'stock_quantity', 'created_at'];
if (!in_array($sortBy, $allowedSort)) $sortBy = 'created_at';

// === Build WHERE conditions ===
$conditions = [];
$params = [];

if ($search !== '') {
    $conditions[] = "(p.product_name LIKE ? OR c.category_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filterCat > 0) {
    $conditions[] = "p.category_id = ?";
    $params[] = $filterCat;
}
if ($filterSup > 0) {
    $conditions[] = "p.supplier_id = ?";
    $params[] = $filterSup;
}
if ($filterStatus === 'Active' || $filterStatus === 'Inactive') {
    $conditions[] = "p.status = ?";
    $params[] = $filterStatus;
}
if ($priceMin !== null) {
    $conditions[] = "p.unit_price >= ?";
    $params[] = $priceMin;
}
if ($priceMax !== null) {
    $conditions[] = "p.unit_price <= ?";
    $params[] = $priceMax;
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

// === Sort column ===
$sortColumn = "p.$sortBy";
if ($sortBy === 'product_name') $sortColumn = "p.product_name";

// === Count total ===
$countSql = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.category_id $where";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = max(1, ceil($total / $limit));

// === Fetch products ===
$sql = "SELECT p.*, c.category_name, s.supplier_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
        $where 
        ORDER BY $sortColumn $sortDir 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// === Load categories & suppliers for filter dropdowns ===
$categories = $pdo->query("SELECT * FROM categories WHERE status='Active' ORDER BY category_name")->fetchAll();
$suppliers  = $pdo->query("SELECT * FROM suppliers WHERE status='Active' ORDER BY supplier_name")->fetchAll();

// === Check if any filter is active ===
$hasFilters = ($filterCat || $filterSup || $filterStatus || $priceMin !== null || $priceMax !== null);

// === Helper functions ===

/**
 * Build sort URL — toggle hướng sắp xếp khi click vào cùng cột
 */
function sortUrl(string $col, string $currentSort, string $currentDir): string {
    $newDir = ($col === $currentSort && $currentDir === 'asc') ? 'desc' : 'asc';
    $params = $_GET;
    $params['sort'] = $col;
    $params['dir'] = $newDir;
    $params['page'] = 1;
    return '?' . http_build_query($params);
}

/**
 * Build sort CSS class — hiển thị mũi tên sắp xếp
 */
function sortClass(string $col, string $currentSort, string $currentDir): string {
    if ($col !== $currentSort) return 'sortable';
    return 'sortable sort-' . $currentDir;
}

/**
 * Build pagination URL — giữ nguyên tất cả params, chỉ đổi page
 */
function pageUrl(int $pg): string {
    $params = $_GET;
    $params['page'] = $pg;
    return '?' . http_build_query($params);
}
