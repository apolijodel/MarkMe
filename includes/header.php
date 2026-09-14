<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'MarkMe';
$fullWidth = $fullWidth ?? false;

// Prompt guests to log in once per session, the first time they land on the site.
if (!is_logged_in() && empty($_SESSION['welcomed'])) {
    $_SESSION['welcomed'] = true;
    if (empty($_SESSION['flash']['reopen_modal'])) {
        flash_set('reopen_modal', 'login');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · MarkMe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="page-shop">
<?php require __DIR__ . '/navbar.php'; ?>
<main class="<?= $fullWidth ? '' : 'container py-4' ?>">
    <?php if (!$fullWidth): ?>
    <?php if ($msg = flash_get('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($msg = flash_get('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php else: ?>
    <?php if ($msg = flash_get('success')): ?>
        <div class="container pt-4"><div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div></div>
    <?php endif; ?>
    <?php if ($msg = flash_get('error')): ?>
        <div class="container pt-4"><div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div></div>
    <?php endif; ?>
    <?php endif; ?>
