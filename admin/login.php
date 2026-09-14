<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in() && is_admin()) {
    redirect('/admin/dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = $identifier !== '' && $password !== '' ? attempt_login($identifier, $password) : null;

    if ($user && $user['role'] === 'admin') {
        redirect('/admin/dashboard.php');
    }

    if ($user && $user['role'] !== 'admin') {
        logout_user();
        $error = 'This account does not have administrator access.';
    } else {
        $error = 'Invalid credentials.';
    }
}

$pageTitle = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · MarkMe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h1 class="h4 mb-4 text-center">MarkMe Admin</h1>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Username or Email</label>
                            <input type="text" name="identifier" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Log In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
