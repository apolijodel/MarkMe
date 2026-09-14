<?php
require_once __DIR__ . '/../includes/auth.php';
require_guest();

$errors = [];
$old = ['username' => '', 'email' => '', 'full_name' => '', 'contact_number' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $old['username'] = trim($_POST['username'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['full_name'] = trim($_POST['full_name'] ?? '');
    $old['contact_number'] = trim($_POST['contact_number'] ?? '');
    $old['address'] = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($old['username'] === '' || !preg_match('/^[A-Za-z0-9_]{3,50}$/', $old['username'])) {
        $errors[] = 'Username must be 3-50 characters (letters, numbers, underscore only).';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }
    if ($old['full_name'] === '') {
        $errors[] = 'Full name is required.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Password confirmation does not match.';
    }

    if (!$errors) {
        $stmt = db()->prepare('SELECT user_id FROM users WHERE username = :u OR email = :e');
        $stmt->execute(['u' => $old['username'], 'e' => $old['email']]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email is already registered.';
        }
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO users (role, username, email, password_hash, full_name, contact_number, address)
             VALUES (\'customer\', :username, :email, :hash, :full_name, :contact_number, :address)'
        );
        $stmt->execute([
            'username' => $old['username'],
            'email' => $old['email'],
            'hash' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $old['full_name'],
            'contact_number' => $old['contact_number'] ?: null,
            'address' => $old['address'] ?: null,
        ]);

        $userId = db()->lastInsertId();
        db()->prepare('INSERT INTO carts (user_id) VALUES (:uid)')->execute(['uid' => $userId]);

        flash_set('success', 'Account created! Please log in.');
        redirect('/customer/login.php');
    }
}

$pageTitle = 'Register';
require __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-visual">
            <div class="brand-mark">MarkMe</div>
            <blockquote>"Join MarkMe and start designing a bookmark that tells your story."</blockquote>
            <div class="auth-shapes">
                <div class="bookmark-shape" style="width:60px;height:140px;background:var(--gold);"></div>
                <div class="bookmark-shape" style="width:60px;height:160px;background:var(--accent);"></div>
                <div class="bookmark-shape" style="width:60px;height:120px;background:var(--cream);"></div>
            </div>
        </div>
        <div class="auth-form-panel">
            <h1>Create an account</h1>
            <p class="subtitle">It only takes a minute to start personalizing.</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= e($old['full_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= e($old['username']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($old['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="<?= e($old['contact_number']) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Delivery Address</label>
                        <textarea name="address" class="form-control" rows="2"><?= e($old['address']) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="8" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg mt-4">Register</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="<?= BASE_URL ?>/customer/login.php">Log in</a></p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
