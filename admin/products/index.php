<?php
/**
 * Products List — Danh sách sản phẩm với tìm kiếm, sắp xếp, lọc & phân trang
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

$pdo = getDBConnection();

// Load filter, search, sort & pagination logic
require_once __DIR__ . '/filter.php';

$pageTitle = 'Quản lý sản phẩm';
$currentPage = 'products';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="table-wrapper">
    <div class="table-header">
        <h2>Danh sách sản phẩm (<?= $total ?>)</h2>
        <div class="table-actions">
            <form method="GET" class="search-box">
                <!-- Preserve filter params in search -->
                <?php if ($filterCat): ?><input type="hidden" name="category" value="<?= $filterCat ?>"><?php endif; ?>
                <?php if ($filterSup): ?><input type="hidden" name="supplier" value="<?= $filterSup ?>"><?php endif; ?>
                <?php if ($filterStatus): ?><input type="hidden" name="status" value="<?= e($filterStatus) ?>"><?php endif; ?>
                <?php if ($priceMin !== null): ?><input type="hidden" name="price_min" value="<?= $priceMin ?>"><?php endif; ?>
                <?php if ($priceMax !== null): ?><input type="hidden" name="price_max" value="<?= $priceMax ?>"><?php endif; ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" placeholder="Tìm sản phẩm..." value="<?= e($search) ?>">
            </form>
            <a href="<?= baseUrl('admin/products/create.php') ?>" class="btn btn-primary btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Thêm mới
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar" id="filterBar">
        <?php if ($search): ?><input type="hidden" name="search" value="<?= e($search) ?>"><?php endif; ?>
        
        <div class="filter-group">
            <label>Danh mục</label>
            <select name="category">
                <option value="0">Tất cả</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= $filterCat == $cat['category_id'] ? 'selected' : '' ?>><?= e($cat['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Nhà cung cấp</label>
            <select name="supplier">
                <option value="0">Tất cả</option>
                <?php foreach ($suppliers as $sup): ?>
                    <option value="<?= $sup['supplier_id'] ?>" <?= $filterSup == $sup['supplier_id'] ? 'selected' : '' ?>><?= e($sup['supplier_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Trạng thái</label>
            <select name="status">
                <option value="">Tất cả</option>
                <option value="Active" <?= $filterStatus === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $filterStatus === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Khoảng giá (₫)</label>
            <div class="price-range">
                <input type="number" name="price_min" placeholder="Từ" step="1000" min="0" value="<?= $priceMin !== null ? $priceMin : '' ?>">
                <span>—</span>
                <input type="number" name="price_max" placeholder="Đến" step="1000" min="0" value="<?= $priceMax !== null ? $priceMax : '' ?>">
            </div>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Lọc
            </button>
            <?php if ($hasFilters || $search): ?>
                <a href="<?= baseUrl('admin/products/index.php') ?>" class="btn btn-secondary btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Xóa lọc
                </a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <h3>Không tìm thấy sản phẩm</h3>
            <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
            <a href="<?= baseUrl('admin/products/create.php') ?>" class="btn btn-primary">Thêm sản phẩm</a>
        </div>
    <?php else: ?>
        <table>
            <thead><tr>
                <th>ID</th>
                <th class="<?= sortClass('product_name', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('product_name', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Sản phẩm <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th>Danh mục</th>
                <th class="<?= sortClass('unit_price', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('unit_price', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Giá <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th class="<?= sortClass('stock_quantity', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('stock_quantity', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Tồn kho <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th>Trạng thái</th>
                <th class="<?= sortClass('created_at', $sortBy, $sortDir) ?>">
                    <a href="<?= sortUrl('created_at', $sortBy, $sortDir) ?>" style="color:inherit;text-decoration:none">
                        Ngày tạo <span class="sort-icon"><span class="arrow arrow-up">▲</span><span class="arrow arrow-down">▼</span></span>
                    </a>
                </th>
                <th>Thao tác</th>
            </tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td>#<?= $p['product_id'] ?></td>
                <td>
                    <div class="product-cell">
                        <?php if ($p['main_image'] && file_exists(__DIR__ . '/../../assets/uploads/' . $p['main_image'])): ?>
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
                <td>
                    <?= formatPrice($p['unit_price']) ?>
                    <?php if ($p['old_price']): ?>
                        <br><small style="color:var(--text-muted);text-decoration:line-through"><?= formatPrice($p['old_price']) ?></small>
                    <?php endif; ?>
                </td>
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
                <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                <td>
                    <div class="action-btns">
                        <a href="<?= baseUrl('admin/products/edit.php?id=' . $p['product_id']) ?>" class="btn btn-secondary btn-icon" title="Sửa">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form id="del-product-<?= $p['product_id'] ?>" method="POST" action="<?= baseUrl('admin/products/delete.php') ?>" style="display:inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= $p['product_id'] ?>">
                            <button type="button" class="btn btn-danger btn-icon" title="Xóa" onclick="confirmDelete('del-product-<?= $p['product_id'] ?>')">
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
        <p>Bạn có chắc chắn muốn xóa sản phẩm này? Hành động không thể hoàn tác.</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="cancelDeleteBtn">Hủy</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
