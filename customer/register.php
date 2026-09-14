<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/customer/index.php');
}

require_guest();
csrf_check();

$returnTo = safe_local_path($_POST['return_to'] ?? null);

$old = [
    'username' => trim($_POST['username'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'full_name' => trim($_POST['full_name'] ?? ''),
    'contact_number' => trim($_POST['contact_number'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
];
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

$errors = [];

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

if ($errors) {
    flash_set('register_errors', $errors);
    flash_set('register_old', $old);
    flash_set('reopen_modal', 'register');
    flash_set('register_return_to', $returnTo);
    redirect($returnTo);
}

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

flash_set('login_success', 'Account created! Please log in below.');
flash_set('login_old_identifier', $old['username']);
flash_set('reopen_modal', 'login');
flash_set('login_return_to', $returnTo);
redirect($returnTo);
