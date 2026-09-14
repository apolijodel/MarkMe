<?php
require_once __DIR__ . '/includes/functions.php';

$featured = db()->query(
    'SELECT * FROM products ORDER BY created_at DESC LIMIT 4'
)->fetchAll();

$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
?>

<div class="p-5 mb-4 bg-light rounded-3 text-center">
    <h1 class="display-5 fw-bold">Design Your Own Bookmark</h1>
    <p class="col-lg-8 mx-auto fs-5">
        Pick a shape, choose a design, upload your favorite photo, and add a personal message —
        MarkMe turns your bookmark into something one of a kind.
    </p>
    <a class="btn btn-primary btn-lg" href="<?= BASE_URL ?>/catalog.php">Shop Now</a>
</div>

<h2 class="h4 mb-3">Featured Products</h2>
<div class="row row-cols-1 row-cols-md-4 g-4">
    <?php foreach ($featured as $product): ?>
        <div class="col">
            <div class="card h-100 product-card">
                <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['image_path']) ?>" class="card-img-top" alt="<?= e($product['name']) ?>">
                <div class="card-body">
                    <h3 class="h6 card-title"><?= e($product['name']) ?></h3>
                    <p class="card-text fw-semibold"><?= format_price($product['price']) ?></p>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$product['product_id'] ?>" class="btn btn-outline-primary btn-sm w-100">View</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
