<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC');
$stmt->execute(['uid' => current_user()['user_id']]);
$orders = $stmt->fetchAll();

$statusColors = [
    'Pending' => 'secondary',
    'Processing' => 'info',
    'Ready for Delivery' => 'warning',
    'Completed' => 'success',
    'Cancelled' => 'danger',
];

$pageTitle = 'My Orders';
require __DIR__ . '/includes/header.php';
?>

<h1 class="h3 mb-4">My Orders</h1>

<?php if (!$orders): ?>
    <p class="text-muted">You haven't placed any orders yet.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= (int)$order['order_id'] ?></td>
                        <td><?= e(date('M j, Y g:i A', strtotime($order['created_at']))) ?></td>
                        <td><?= format_price($order['total_amount']) ?></td>
                        <td><span class="badge bg-<?= $statusColors[$order['status']] ?? 'secondary' ?>"><?= e($order['status']) ?></span></td>
                        <td><a href="<?= BASE_URL ?>/order_detail.php?id=<?= (int)$order['order_id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
