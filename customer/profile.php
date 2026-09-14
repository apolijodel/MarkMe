<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$userId = current_user()['user_id'];
$stmt = db()->prepare('SELECT * FROM users WHERE user_id = :id');
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

$errors = [];
$pwErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    csrf_check();
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($full_name === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) $errors[] = 'Invalid username format.';

    if (!$errors) {
        $stmt = db()->prepare('SELECT user_id FROM users WHERE (username = :u OR email = :e) AND user_id != :id');
        $stmt->execute(['u' => $username, 'e' => $email, 'id' => $userId]);
        if ($stmt->fetch()) {
            $errors[] = 'That username or email is already taken.';
        }
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'UPDATE users SET full_name = :full_name, email = :email, username = :username,
             contact_number = :contact_number, address = :address WHERE user_id = :id'
        );
        $stmt->execute([
            'full_name' => $full_name,
            'email' => $email,
            'username' => $username,
            'contact_number' => $contact_number ?: null,
            'address' => $address ?: null,
            'id' => $userId,
        ]);

        $_SESSION['user']['full_name'] = $full_name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['username'] = $username;

        flash_set('success', 'Profile updated.');
        redirect('/customer/profile.php');
    }

    $user = array_merge($user, compact('full_name', 'email', 'username', 'contact_number', 'address'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    csrf_check();
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!password_verify($current, $user['password_hash'])) {
        $pwErrors[] = 'Current password is incorrect.';
    }
    if (strlen($new) < 8) {
        $pwErrors[] = 'New password must be at least 8 characters.';
    }
    if ($new !== $confirm) {
        $pwErrors[] = 'New password confirmation does not match.';
    }

    if (!$pwErrors) {
        $stmt = db()->prepare('UPDATE users SET password_hash = :hash WHERE user_id = :id');
        $stmt->execute(['hash' => password_hash($new, PASSWORD_DEFAULT), 'id' => $userId]);
        flash_set('success', 'Password changed successfully.');
        redirect('/customer/profile.php');
    }
}

$pageTitle = 'My Profile';
require __DIR__ . '/../includes/header.php';
?>

<div class="row g-4">
    <div class="col-md-6">
        <h2 class="h5 mb-3">Profile Information</h2>
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update_profile">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= e($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="<?= e($user['contact_number']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Delivery Address</label>
                <textarea name="address" class="form-control" rows="2"><?= e($user['address']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <div class="col-md-6">
        <h2 class="h5 mb-3">Change Password</h2>
        <?php if ($pwErrors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ($pwErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="change_password">
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" minlength="8" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" minlength="8" required>
            </div>
            <button type="submit" class="btn btn-outline-primary">Change Password</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
