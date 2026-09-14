<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$productId = (int)($_GET['id'] ?? $_POST['product_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM products WHERE product_id = :id');
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    flash_set('error', 'Product not found.');
    redirect('/admin/products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $addQty = max(0, (int)($_POST['add_quantity'] ?? 0));
    if ($addQty > 0) {
        db()->prepare('UPDATE products SET stock_quantity = stock_quantity + :qty WHERE product_id = :id')
            ->execute(['qty' => $addQty, 'id' => $productId]);
        flash_set('success', "Added {$addQty} units. New stock: " . ((int)$product['stock_quantity'] + $addQty));
    }
    redirect('/admin/products.php');
}

$pageTitle = 'Restock Product';
require __DIR__ . '/includes/admin_header.php';
?>

<h1 class="h3 mb-4">Restock: <?= e($product['name']) ?></h1>

<div class="row justify-content-center">
    <div class="col-md-5">
        <p>Current stock: <strong><?= (int)$product['stock_quantity'] ?></strong></p>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
            <div class="mb-3">
                <label class="form-label">Quantity to Add</label>
                <input type="number" name="add_quantity" class="form-control" min="1" required autofocus>
            </div>
            <button type="submit" class="btn btn-success">Add Stock</button>
            <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
