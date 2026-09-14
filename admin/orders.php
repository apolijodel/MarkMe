<?php
$pageTitle = 'Orders';
require __DIR__ . '/includes/admin_header.php';

$statusFilter = $_GET['status'] ?? '';
$validStatuses = ['Pending', 'Processing', 'Ready for Delivery', 'Completed', 'Cancelled'];

$sql = 'SELECT o.*, u.username FROM orders o JOIN users u ON u.user_id = o.user_id';
$params = [];
if (in_array($statusFilter, $validStatuses, true)) {
    $sql .= ' WHERE o.status = :status';
    $params['status'] = $statusFilter;
}
$sql .= ' ORDER BY o.created_at DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statusColors = [
    'Pending' => 'secondary', 'Processing' => 'info', 'Ready for Delivery' => 'warning',
    'Completed' => 'success', 'Cancelled' => 'danger',
];
?>

<h1 class="h3 mb-4">Orders</h1>

<form method="get" class="mb-3">
    <select name="status" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <?php foreach ($validStatuses as $s): ?>
            <option value="<?= e($s) ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<table class="table align-middle">
    <thead><tr><th>Order #</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= (int)$order['order_id'] ?></td>
                <td><?= e($order['username']) ?></td>
                <td class="small text-muted"><?= e(date('M j, Y g:i A', strtotime($order['created_at']))) ?></td>
                <td><?= format_price($order['total_amount']) ?></td>
                <td><span class="badge bg-<?= $statusColors[$order['status']] ?? 'secondary' ?>"><?= e($order['status']) ?></span></td>
                <td><a href="<?= BASE_URL ?>/admin/order_view.php?id=<?= (int)$order['order_id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$orders): ?><tr><td colspan="6" class="text-muted">No orders found.</td></tr><?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
