<?php
require_once __DIR__ . '/includes/functions.php';

$search = trim($_GET['q'] ?? '');
$categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
$sort = $_GET['sort'] ?? '';

$where = [];
$params = [];

if ($search !== '') {
    $where[] = 'p.name LIKE :search';
    $params['search'] = '%' . $search . '%';
}
if ($categoryId) {
    $where[] = 'p.category_id = :category';
    $params['category'] = $categoryId;
}

$sql = 'SELECT p.*, c.name AS category_name FROM products p
        JOIN categories c ON c.category_id = p.category_id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$orderMap = [
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name_asc' => 'p.name ASC',
];
$sql .= ' ORDER BY ' . ($orderMap[$sort] ?? 'p.created_at DESC');

$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();

$pageTitle = 'Catalog';
require __DIR__ . '/includes/header.php';
?>

<h1 class="h3 mb-4">Product Catalog</h1>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-5">
        <input type="text" name="q" class="form-control" placeholder="Search bookmarks..." value="<?= e($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="category" class="form-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['category_id'] ?>" <?= $categoryId === (int)$cat['category_id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="sort" class="form-select">
            <option value="">Sort by</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name: A to Z</option>
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100">Go</button>
    </div>
</form>

<?php if (!$products): ?>
    <p class="text-muted">No products match your search.</p>
<?php endif; ?>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($products as $product): ?>
        <?php $outOfStock = (int)$product['stock_quantity'] <= 0; ?>
        <div class="col">
            <div class="card h-100 product-card <?= $outOfStock ? 'stock-badge-out' : '' ?>">
                <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['image_path']) ?>" class="card-img-top" alt="<?= e($product['name']) ?>">
                <div class="card-body">
                    <span class="badge bg-secondary mb-2"><?= e($product['category_name']) ?></span>
                    <h3 class="h6 card-title"><?= e($product['name']) ?></h3>
                    <p class="card-text fw-semibold"><?= format_price($product['price']) ?></p>
                    <?php if ($outOfStock): ?>
                        <span class="badge bg-danger">Out of Stock</span>
                    <?php else: ?>
                        <span class="badge bg-success"><?= (int)$product['stock_quantity'] ?> in stock</span>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$product['product_id'] ?>" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
