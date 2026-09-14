<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/customer/index.php');
}

require_guest();
csrf_check();

$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';
$returnTo = safe_local_path($_POST['return_to'] ?? null);

$user = $identifier !== '' && $password !== '' ? attempt_login($identifier, $password) : null;

if ($user) {
    redirect($user['role'] === 'admin' ? '/admin/dashboard.php' : $returnTo);
}

flash_set('login_error', 'Invalid username/email or password.');
flash_set('login_old_identifier', $identifier);
flash_set('reopen_modal', 'login');
flash_set('login_return_to', $returnTo);
redirect($returnTo);
