<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/customers.php');
}
csrf_check();

$userId = (int)($_POST['user_id'] ?? 0);

$stmt = db()->prepare("SELECT is_active FROM users WHERE user_id = :id AND role = 'customer'");
$stmt->execute(['id' => $userId]);
$row = $stmt->fetch();

if ($row) {
    $newStatus = $row['is_active'] ? 0 : 1;
    db()->prepare('UPDATE users SET is_active = :status WHERE user_id = :id')
        ->execute(['status' => $newStatus, 'id' => $userId]);
    flash_set('success', $newStatus ? 'Customer account reactivated.' : 'Customer account deactivated.');
} else {
    flash_set('error', 'Customer not found.');
}

redirect('/admin/customers.php');
