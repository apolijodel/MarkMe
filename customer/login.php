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

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-visual">
            <div class="brand-mark">MarkMe</div>
            <blockquote>"Every page you've loved deserves a bookmark that's just as personal."</blockquote>
            <div class="auth-shapes">
                <div class="bookmark-shape" style="width:60px;height:140px;background:var(--gold);"></div>
                <div class="bookmark-shape" style="width:60px;height:160px;background:var(--accent);"></div>
                <div class="bookmark-shape" style="width:60px;height:120px;background:var(--cream);"></div>
            </div>
        </div>
        <div class="auth-form-panel">
            <h1>Welcome back</h1>
            <p class="subtitle">Log in to keep designing and tracking your bookmarks.</p>

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
                <button type="submit" class="btn btn-primary w-100 btn-lg">Log In</button>
            </form>
            <p class="auth-switch">No account yet? <a href="<?= BASE_URL ?>/customer/register.php">Register</a></p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
