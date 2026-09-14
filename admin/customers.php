<?php
$pageTitle = 'Customers';
require __DIR__ . '/includes/admin_header.php';

$search = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM users WHERE role = 'customer'";
$params = [];
if ($search !== '') {
    $sql .= ' AND (username LIKE :s OR email LIKE :s OR full_name LIKE :s)';
    $params['s'] = '%' . $search . '%';
}
$sql .= ' ORDER BY created_at DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();
?>

<h1 class="h3 mb-4">Customers</h1>

<form method="get" class="mb-3 row g-2">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Search by name, username, or email" value="<?= e($search) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary">Search</button>
    </div>
</form>

<table class="table align-middle">
    <thead><tr><th>Username</th><th>Full Name</th><th>Email</th><th>Contact</th><th>Status</th><th>Joined</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= e($c['username']) ?></td>
                <td><?= e($c['full_name']) ?></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['contact_number']) ?></td>
                <td><span class="badge bg-<?= $c['is_active'] ? 'success' : 'secondary' ?>"><?= $c['is_active'] ? 'Active' : 'Deactivated' ?></span></td>
                <td class="small text-muted"><?= e(date('M j, Y', strtotime($c['created_at']))) ?></td>
                <td>
                    <form method="post" action="<?= BASE_URL ?>/admin/customer_toggle.php" onsubmit="return confirm('<?= $c['is_active'] ? 'Deactivate' : 'Reactivate' ?> this account?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="user_id" value="<?= (int)$c['user_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-<?= $c['is_active'] ? 'danger' : 'success' ?>">
                            <?= $c['is_active'] ? 'Deactivate' : 'Reactivate' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$customers): ?><tr><td colspan="7" class="text-muted">No customers found.</td></tr><?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
