<?php
$pageTitle = 'Designs';
require __DIR__ . '/includes/admin_header.php';

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = db()->prepare('SELECT * FROM designs WHERE design_id = :id');
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$designs = db()->query('SELECT * FROM designs ORDER BY name')->fetchAll();
?>

<h1 class="h3 mb-4">Bookmark Designs</h1>
<p class="text-muted">These template designs appear on the product page so customers can pick a base design for their personalized bookmark.</p>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h6"><?= $editing ? 'Edit Design' : 'Add Design' ?></h2>
                <form method="post" action="<?= BASE_URL ?>/admin/design_save.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php if ($editing): ?><input type="hidden" name="design_id" value="<?= (int)$editing['design_id'] ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= e($editing['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <?php if ($editing): ?>
                            <div class="mb-2"><img src="<?= BASE_URL ?>/uploads/designs/<?= e($editing['image_path']) ?>" width="60" class="rounded"></div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" <?= $editing ? '' : 'required' ?>>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editing ? 'Save Changes' : 'Add Design' ?></button>
                    <?php if ($editing): ?><a href="<?= BASE_URL ?>/admin/designs.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row row-cols-2 row-cols-md-3 g-3">
            <?php foreach ($designs as $design): ?>
                <div class="col">
                    <div class="card h-100 text-center">
                        <img src="<?= BASE_URL ?>/uploads/designs/<?= e($design['image_path']) ?>" class="card-img-top p-2" style="height:100px;object-fit:contain;">
                        <div class="card-body p-2">
                            <div class="small fw-semibold"><?= e($design['name']) ?></div>
                            <div class="mt-2">
                                <a href="<?= BASE_URL ?>/admin/designs.php?edit=<?= (int)$design['design_id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="post" action="<?= BASE_URL ?>/admin/design_delete.php" class="d-inline" onsubmit="return confirm('Delete this design?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="design_id" value="<?= (int)$design['design_id'] ?>">
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
