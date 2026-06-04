<?php
/**
 * Users List — Danh sách người dùng với tìm kiếm, sắp xếp & phân trang
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();

// Search, Sort & Pagination
$search  = trim($_GET['search'] ?? '');
$sortBy  = $_GET['sort'] ?? 'created_at';
$sortDir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$page    = max(1, (int)($_GET['page'] ?? 1));
$limit   = 10;
$offset  = ($page - 1) * $limit;

// Whitelist sort columns
$allowedSort = ['username', 'email', 'role', 'status', 'created_at'];
if (!in_array($sortBy, $allowedSort)) $sortBy = 'created_at';

$where = '';
$params = [];

if ($search !== '') {
    $where = "WHERE username LIKE ? OR email LIKE ? OR role LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$countSql = "SELECT COUNT(*) FROM users $where";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = max(1, ceil($total / $limit));

$sql = "SELECT * FROM users $where ORDER BY $sortBy $sortDir LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle = 'Quản lý người dùng';
$currentPage = 'users';

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
        <h2>Danh sách người dùng (<?= $total ?>)</h2>
        <div class="table-actions">
            <form method="GET" class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" placeholder="Tìm người dùng..." value="<?= e($search) ?>">
            </form>
            <a href="<?= baseUrl('admin/users/create.php') ?>" class="btn btn-primary btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Thêm mới
            </a>
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <h3>Chưa có người dùng nào</h3>
            <p>Hãy thêm người dùng đầu tiên</p>
        </div>
    <?php else: ?>
        <table>
            <thead><tr>
                <th>ID</th>
                <th class="<?= sortClass('username', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('username', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Username <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th class="<?= sortClass('email', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('email', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Email <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th class="<?= sortClass('role', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('role', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Role <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
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
            <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?= $u['user_id'] ?></td>
                <td><strong><?= e($u['username']) ?></strong></td>
                <td><?= e($u['email'] ?? '—') ?></td>
                <td>
                    <?php
                    $roleBadge = 'badge-info';
                    if ($u['role'] === 'Admin') $roleBadge = 'badge-warning';
                    elseif ($u['role'] === 'Customer') $roleBadge = 'badge-success';
                    ?>
                    <span class="badge <?= $roleBadge ?>"><?= e($u['role']) ?></span>
                </td>
                <td><span class="badge <?= $u['status'] === 'Active' ? 'badge-success' : 'badge-danger' ?>"><?= $u['status'] ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                <td>
                    <div class="action-btns">
                        <a href="<?= baseUrl('admin/users/edit.php?id=' . $u['user_id']) ?>" class="btn btn-secondary btn-icon" title="Sửa">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <?php if ($u['user_id'] !== $_SESSION['user_id']): ?>
                        <form id="del-user-<?= $u['user_id'] ?>" method="POST" action="<?= baseUrl('admin/users/delete.php') ?>" style="display:inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= $u['user_id'] ?>">
                            <button type="button" class="btn btn-danger btn-icon" title="Xóa" onclick="confirmDelete('del-user-<?= $u['user_id'] ?>')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                            </button>
                        </form>
                        <?php endif; ?>
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
        <p>Bạn có chắc chắn muốn xóa người dùng này? Hành động không thể hoàn tác.</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="cancelDeleteBtn">Hủy</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
