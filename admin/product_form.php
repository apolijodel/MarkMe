<?php
$pageTitle = 'Product Form';
require __DIR__ . '/includes/admin_header.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$product = null;
if ($productId) {
    $stmt = db()->prepare('SELECT * FROM products WHERE product_id = :id');
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch() ?: null;
}

$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
?>

<h1 class="h3 mb-4"><?= $product ? 'Edit Product' : 'Add Product' ?></h1>

<div class="row justify-content-center">
    <div class="col-md-7">
        <form method="post" action="<?= BASE_URL ?>/admin/product_save.php" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php if ($product): ?><input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>"><?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?= e($product['name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['category_id'] ?>" <?= ($product['category_id'] ?? null) == $cat['category_id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= e($product['description'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Price (₱)</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?= e((string)($product['price'] ?? '')) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" min="0" name="stock_quantity" class="form-control" value="<?= e((string)($product['stock_quantity'] ?? '0')) ?>" required>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_customizable" class="form-check-input" id="is_customizable" <?= ($product['is_customizable'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_customizable">Customers can personalize this product (shape, design, photo, text)</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Product Image</label>
                <?php if ($product): ?>
                    <div class="mb-2"><img src="<?= BASE_URL ?>/uploads/products/<?= e($product['image_path']) ?>" width="80" class="rounded"></div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" <?= $product ? '' : 'required' ?>>
                <div class="form-text">JPG, PNG, or WEBP. Max 2MB. <?= $product ? 'Leave empty to keep the current image.' : '' ?></div>
            </div>
            <button type="submit" class="btn btn-primary"><?= $product ? 'Save Changes' : 'Add Product' ?></button>
            <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
