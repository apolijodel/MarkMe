<?php
/**
 * Shared screen for the two image-option catalogs (shapes and designs) —
 * they behave identically, so they share one layout.
 *
 * Expects $opt:
 *   table, idColumn, folder, singular, plural, blurb, saveAction, deleteAction, icon
 */
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editing = null;
if ($editId) {
    $stmt = db()->prepare("SELECT * FROM {$opt['table']} WHERE {$opt['idColumn']} = :id");
    $stmt->execute(['id' => $editId]);
    $editing = $stmt->fetch() ?: null;
}

$rows = db()->query("SELECT * FROM {$opt['table']} ORDER BY name")->fetchAll();

/* Where the edited row sits in the list, so the carousel can open on it
   rather than at the start with the selection paged off screen. */
$selectedIndex = 0;
if ($editing) {
    foreach ($rows as $i => $row) {
        if ((int)$row[$opt['idColumn']] === (int)$editing[$opt['idColumn']]) {
            $selectedIndex = $i;
            break;
        }
    }
}

$self = BASE_URL . '/admin/' . basename($_SERVER['SCRIPT_NAME']);
?>

<div class="page-head">
    <div>
        <p class="eyebrow eyebrow-muted">Catalog</p>
        <h2 style="margin-top:var(--s-2)"><?= e($opt['plural']) ?></h2>
        <p><?= e($opt['blurb']) ?></p>
    </div>
</div>

<!-- Two panels side by side on one row: the catalog you browse, and the form
     you type into. Separate cards because they are separate jobs, but level
     with each other so adding something and seeing what is already there do
     not sit a scroll apart. They stack below 992px, the same width at which
     every other two-column screen here folds. -->
<div class="option-layout">
<section class="card card-flush option-catalog">
    <div class="panel-head">
        <h2><?= e($opt['plural']) ?></h2>
        <p class="form-text">
            <?= count($rows) ?> in the catalog
        </p>
    </div>
    <div class="panel-body">
        <?php if (!$rows): ?>
            <div class="empty-state option-catalog-empty">
                <span class="empty-icon"><?= icon($opt['icon'], 24) ?></span>
                <h3>No <?= e(strtolower($opt['plural'])) ?> yet</h3>
                <p>Add your first one in the form below.</p>
            </div>
        <?php else: ?>
            <!-- Four to a view rather than running down the page: these are
                 pictures you browse, and a short row is faster to scan. -->
            <div class="carousel admin-carousel option-tile-carousel" data-carousel
                 data-per-view="4 3 2"
                 data-min-card="96"
                 data-carousel-start="<?= (int)$selectedIndex ?>"
                 data-carousel-item=".option-tile">
                <button type="button" class="carousel-nav prev" data-carousel-prev
                        aria-label="Previous <?= e(strtolower($opt['plural'])) ?>"><?= icon('chevron-left', 18) ?></button>
                <div class="carousel-viewport">
                    <div class="carousel-track" data-carousel-track>
                        <?php foreach ($rows as $row): ?>
                            <?php $rowId = (int)$row[$opt['idColumn']]; ?>
                            <?php $isSelected = $editing
                                && (int)$editing[$opt['idColumn']] === $rowId; ?>
                            <article class="option-tile<?= $isSelected ? ' is-selected' : '' ?>"
                                     <?= $isSelected ? 'aria-current="true"' : '' ?>>
                                <?php if ($isSelected): ?>
                                    <span class="option-tile-flag" aria-hidden="true"><?= icon('check', 13) ?></span>
                                    <span class="visually-hidden">Currently being edited</span>
                                <?php endif; ?>
                                <div class="option-tile-media">
                                    <img src="<?= upload_url($opt['folder'], $row['image_path']) ?>"
                                         alt="<?= e($row['name']) ?>" loading="lazy" decoding="async">
                                </div>
                                <div class="option-tile-body">
                                    <h3 class="option-tile-name"><?= e($row['name']) ?></h3>
                                    <div class="option-tile-actions">
                                        <a class="btn-icon btn-icon-sm" href="<?= e($self) ?>?edit=<?= $rowId ?>"
                                           aria-label="Edit <?= e($row['name']) ?>"><?= icon('edit', 14) ?></a>
                                        <form method="post" action="<?= BASE_URL ?>/admin/<?= e($opt['deleteAction']) ?>"
                                              data-confirm
                                              data-confirm-title="Delete <?= e(strtolower($opt['singular'])) ?>?"
                                              data-confirm-body="Delete &quot;<?= e($row['name']) ?>&quot;?"
                                              data-confirm-action="Delete"
                                              data-confirm-tone="danger">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="<?= e($opt['idColumn']) ?>" value="<?= $rowId ?>">
                                            <button type="submit" class="btn-icon btn-icon-sm btn-icon-danger"
                                                    aria-label="Delete <?= e($row['name']) ?>"><?= icon('trash', 14) ?></button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="button" class="carousel-nav next" data-carousel-next
                        aria-label="More <?= e(strtolower($opt['plural'])) ?>"><?= icon('chevron-right', 18) ?></button>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php /* Its own panel. The same form does both jobs: empty it adds, populated
         it edits whichever tile you picked, so there is one set of fields and
         one upload path to keep working rather than two. */ ?>
<section class="card card-flush option-form-panel">
    <div class="panel-head">
        <h2><?= $editing
            ? 'Edit ' . e(strtolower($opt['singular']))
            : 'Add ' . e(strtolower($opt['singular'])) ?></h2>
        <?php if ($editing): ?>
            <p class="form-text"><?= e($editing['name']) ?></p>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <form method="post" action="<?= BASE_URL ?>/admin/<?= e($opt['saveAction']) ?>"
              enctype="multipart/form-data" class="option-form">
            <?= csrf_field() ?>
            <?php if ($editing): ?>
                <input type="hidden" name="<?= e($opt['idColumn']) ?>" value="<?= (int)$editing[$opt['idColumn']] ?>">
            <?php endif; ?>

            <div class="field-group">
                <label class="form-label" for="optName">Name</label>
                <input type="text" id="optName" name="name" class="form-control"
                       value="<?= e($editing['name'] ?? '') ?>" required>
            </div>

            <div class="field-group">
                <label class="form-label" for="optImage">Image</label>
                <?php if ($editing): ?>
                    <img class="thumb option-form-thumb"
                         src="<?= upload_url($opt['folder'], $editing['image_path']) ?>"
                         width="56" height="56" alt="Current <?= e(strtolower($opt['singular'])) ?> image"
                         loading="lazy" decoding="async">
                <?php endif; ?>
                <input type="file" id="optImage" name="image" class="form-control"
                       accept=".jpg,.jpeg,.png,.webp" <?= $editing ? '' : 'required' ?>>
                <p class="form-text">
                    JPG, PNG or WEBP. Max 2MB.
                    <?= $editing ? 'Leave empty to keep the current image.' : '' ?>
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <?= $editing ? 'Save changes' : 'Add ' . e(strtolower($opt['singular'])) ?>
                </button>
                <?php if ($editing): ?>
                    <a href="<?= e($self) ?>" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>
</div>
