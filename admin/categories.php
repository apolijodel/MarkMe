<?php
$pageTitle = 'Categories';
require __DIR__ . '/includes/admin_header.php';

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = db()->prepare('SELECT * FROM categories WHERE category_id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$categories = db()->query(
    'SELECT c.*, COUNT(p.product_id) AS product_count
     FROM categories c LEFT JOIN products p ON p.category_id = c.category_id
     GROUP BY c.category_id ORDER BY c.name'
)->fetchAll();
?>

<h1 class="h3 mb-4">Categories</h1>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h6"><?= $editing ? 'Edit Category' : 'Add Category' ?></h2>
                <form method="post" action="<?= BASE_URL ?>/admin/category_save.php">
                    <?= csrf_field() ?>
                    <?php if ($editing): ?><input type="hidden" name="category_id" value="<?= (int)$editing['category_id'] ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= e($editing['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"><?= e($editing['description'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editing ? 'Save Changes' : 'Add Category' ?></button>
                    <?php if ($editing): ?><a href="<?= BASE_URL ?>/admin/categories.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <table class="table">
            <thead><tr><th>Name</th><th>Description</th><th>Products</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= e($cat['name']) ?></td>
                        <td class="small text-muted"><?= e($cat['description']) ?></td>
                        <td><?= (int)$cat['product_count'] ?></td>
                        <td class="text-nowrap">
                            <a href="<?= BASE_URL ?>/admin/categories.php?edit=<?= (int)$cat['category_id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="post" action="<?= BASE_URL ?>/admin/category_delete.php" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="category_id" value="<?= (int)$cat['category_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" <?= $cat['product_count'] > 0 ? 'disabled title="Cannot delete: products are assigned to this category"' : '' ?>>Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
