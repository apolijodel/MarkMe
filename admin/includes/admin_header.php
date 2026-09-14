<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();
$pageTitle = $pageTitle ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · MarkMe Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/admin/dashboard.php">MarkMe Admin</a>
        <span class="navbar-text text-light">
            <?= e(current_user()['username']) ?> &middot;
            <a class="text-light" href="<?= BASE_URL ?>/index.php">View Site</a> &middot;
            <a class="text-light" href="<?= BASE_URL ?>/admin/logout.php">Logout</a>
        </span>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <?php require __DIR__ . '/admin_sidebar.php'; ?>
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
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
