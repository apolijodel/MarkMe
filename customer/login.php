<?php
require_once __DIR__ . '/../includes/auth.php';
require_guest();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = $identifier !== '' && $password !== '' ? attempt_login($identifier, $password) : null;

    if ($user) {
        redirect($user['role'] === 'admin' ? '/admin/dashboard.php' : '/customer/index.php');
    }
    $error = 'Invalid username/email or password.';
}

$pageTitle = 'Login';
require __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="h3 mb-4">Log In</h1>

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
            <button type="submit" class="btn btn-primary w-100">Log In</button>
        </form>
        <p class="mt-3">No account yet? <a href="<?= BASE_URL ?>/customer/register.php">Register</a></p>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
