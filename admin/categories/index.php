<?php
/**
 * Categories List — Danh sách danh mục với tìm kiếm, sắp xếp & phân trang
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

$pdo = getDBConnection();

// Search, Sort & Pagination
$search  = trim($_GET['search'] ?? '');
$sortBy  = $_GET['sort'] ?? 'created_at';
$sortDir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$page    = max(1, (int)($_GET['page'] ?? 1));
$limit   = 10;
$offset  = ($page - 1) * $limit;

// Whitelist sort columns
$allowedSort = ['category_name', 'product_count', 'status', 'created_at'];
if (!in_array($sortBy, $allowedSort)) $sortBy = 'created_at';

$where = '';
$params = [];

if ($search !== '') {
    $where = "WHERE c.category_name LIKE ? OR c.description LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$countSql = "SELECT COUNT(*) FROM categories c $where";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = max(1, ceil($total / $limit));

// Sort column mapping
$sortColumn = "c.$sortBy";
if ($sortBy === 'product_count') $sortColumn = "product_count";

// Đếm số sản phẩm mỗi danh mục
$sql = "SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.category_id) AS product_count 
        FROM categories c 
        $where 
        ORDER BY $sortColumn $sortDir 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$categories = $stmt->fetchAll();

$pageTitle = 'Quản lý danh mục';
$currentPage = 'categories';

// Sort helpers
function sortUrl(string $col, string $currentSort, string $currentDir): string {
    $newDir = ($col === $currentSort && $currentDir === 'asc') ? 'desc' : 'asc';
    $params = $_GET;
    $params['sort'] = $col;
    $params['dir'] = $newDir;
    $params['page'] = 1;
    return '?' . http_build_query($params);
}
function sortClass(string $col, string $currentSort, string $currentDir): string {
    if ($col !== $currentSort) return 'sortable';
    return 'sortable sort-' . $currentDir;
}
function pageUrl(int $pg): string {
    $params = $_GET;
    $params['page'] = $pg;
    return '?' . http_build_query($params);
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="table-wrapper">
    <div class="table-header">
        <h2>Danh sách danh mục (<?= $total ?>)</h2>
        <div class="table-actions">
            <form method="GET" class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" placeholder="Tìm danh mục..." value="<?= e($search) ?>">
            </form>
            <a href="<?= baseUrl('admin/categories/create.php') ?>" class="btn btn-primary btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Thêm mới
            </a>
        </div>
    </div>

    <?php if (empty($categories)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            <h3>Chưa có danh mục nào</h3>
            <p>Hãy thêm danh mục đầu tiên</p>
            <a href="<?= baseUrl('admin/categories/create.php') ?>" class="btn btn-primary">Thêm danh mục</a>
        </div>
    <?php else: ?>
        <table>
            <thead><tr>
                <th>ID</th>
                <th class="<?= sortClass('category_name', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('category_name', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Tên danh mục <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th>Mô tả</th>
                <th class="<?= sortClass('product_count', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('product_count', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Số sản phẩm <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th class="<?= sortClass('status', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('status', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Trạng thái <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th class="<?= sortClass('created_at', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('created_at', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Ngày tạo <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th>Thao tác</th>
            </tr></thead>
            <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td>#<?= $cat['category_id'] ?></td>
                <td><strong><?= e($cat['category_name']) ?></strong></td>
                <td style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= e($cat['description'] ?? '—') ?></td>
                <td><span class="badge badge-info"><?= $cat['product_count'] ?></span></td>
                <td><span class="badge <?= $cat['status'] === 'Active' ? 'badge-success' : 'badge-danger' ?>"><?= $cat['status'] ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($cat['created_at'])) ?></td>
                <td>
                    <div class="action-btns">
                        <a href="<?= baseUrl('admin/categories/edit.php?id=' . $cat['category_id']) ?>" class="btn btn-secondary btn-icon" title="Sửa">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form id="del-cat-<?= $cat['category_id'] ?>" method="POST" action="<?= baseUrl('admin/categories/delete.php') ?>" style="display:inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= $cat['category_id'] ?>">
                            <button type="button" class="btn btn-danger btn-icon" title="Xóa" onclick="confirmDelete('del-cat-<?= $cat['category_id'] ?>')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= pageUrl($page - 1) ?>">&laquo;</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="active-page"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= pageUrl($i) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="<?= pageUrl($page + 1) ?>">&raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h3>Xác nhận xóa</h3>
        <p>Bạn có chắc chắn muốn xóa danh mục này? Các sản phẩm thuộc danh mục sẽ bị mất liên kết.</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="cancelDeleteBtn">Hủy</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
