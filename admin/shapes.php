<?php
$pageTitle = 'Shapes';
require __DIR__ . '/includes/admin_header.php';

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = db()->prepare('SELECT * FROM shapes WHERE shape_id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$shapes = db()->query('SELECT * FROM shapes ORDER BY name')->fetchAll();
?>

<h1 class="h3 mb-4">Bookmark Shapes</h1>
<p class="text-muted">These options appear on the product page so customers can pick a shape for their personalized bookmark.</p>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h6"><?= $editing ? 'Edit Shape' : 'Add Shape' ?></h2>
                <form method="post" action="<?= BASE_URL ?>/admin/shape_save.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php if ($editing): ?><input type="hidden" name="shape_id" value="<?= (int)$editing['shape_id'] ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= e($editing['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <?php if ($editing): ?>
                            <div class="mb-2"><img src="<?= BASE_URL ?>/uploads/shapes/<?= e($editing['image_path']) ?>" width="60" class="rounded"></div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" <?= $editing ? '' : 'required' ?>>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editing ? 'Save Changes' : 'Add Shape' ?></button>
                    <?php if ($editing): ?><a href="<?= BASE_URL ?>/admin/shapes.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row row-cols-2 row-cols-md-3 g-3">
            <?php foreach ($shapes as $shape): ?>
                <div class="col">
                    <div class="card h-100 text-center">
                        <img src="<?= BASE_URL ?>/uploads/shapes/<?= e($shape['image_path']) ?>" class="card-img-top p-2" style="height:100px;object-fit:contain;">
                        <div class="card-body p-2">
                            <div class="small fw-semibold"><?= e($shape['name']) ?></div>
                            <div class="mt-2">
                                <a href="<?= BASE_URL ?>/admin/shapes.php?edit=<?= (int)$shape['shape_id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="post" action="<?= BASE_URL ?>/admin/shape_delete.php" class="d-inline" onsubmit="return confirm('Delete this shape?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="shape_id" value="<?= (int)$shape['shape_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
