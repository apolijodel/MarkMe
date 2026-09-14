<?php
$pageTitle = 'Products';
require __DIR__ . '/includes/admin_header.php';

$products = db()->query(
    'SELECT p.*, c.name AS category_name FROM products p
     JOIN categories c ON c.category_id = p.category_id
     ORDER BY p.created_at DESC'
)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Products</h1>
    <a href="<?= BASE_URL ?>/admin/product_form.php" class="btn btn-primary">Add Product</a>
</div>

<table class="table align-middle">
    <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Customizable</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><img src="<?= BASE_URL ?>/uploads/products/<?= e($product['image_path']) ?>" width="50" height="50" style="object-fit:cover" class="rounded"></td>
                <td><?= e($product['name']) ?></td>
                <td><?= e($product['category_name']) ?></td>
                <td><?= format_price($product['price']) ?></td>
                <td>
                    <?php if ($product['stock_quantity'] == 0): ?>
                        <span class="badge bg-danger">Out of Stock</span>
                    <?php elseif ($product['stock_quantity'] <= 5): ?>
                        <span class="badge bg-warning text-dark"><?= (int)$product['stock_quantity'] ?> (low)</span>
                    <?php else: ?>
                        <?= (int)$product['stock_quantity'] ?>
                    <?php endif; ?>
                </td>
                <td><?= $product['is_customizable'] ? 'Yes' : 'No' ?></td>
                <td class="text-nowrap">
                    <a href="<?= BASE_URL ?>/admin/product_form.php?id=<?= (int)$product['product_id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <a href="<?= BASE_URL ?>/admin/product_restock.php?id=<?= (int)$product['product_id'] ?>" class="btn btn-sm btn-outline-success">Restock</a>
                    <form method="post" action="<?= BASE_URL ?>/admin/product_delete.php" class="d-inline" onsubmit="return confirm('Delete this product?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$products): ?><tr><td colspan="7" class="text-muted">No products yet.</td></tr><?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
