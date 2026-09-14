<?php
$pageTitle = 'Order Details';
require __DIR__ . '/includes/admin_header.php';

$orderId = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT o.*, u.username, u.email FROM orders o JOIN users u ON u.user_id = o.user_id WHERE o.order_id = :id');
$stmt->execute(['id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    echo '<p>Order not found.</p>';
    require __DIR__ . '/includes/admin_footer.php';
    exit;
}

$itemStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = :id');
$itemStmt->execute(['id' => $orderId]);
$items = $itemStmt->fetchAll();

// Logical status workflow: only forward transitions (or cancellation) are allowed.
$transitions = [
    'Pending' => ['Processing', 'Cancelled'],
    'Processing' => ['Ready for Delivery', 'Cancelled'],
    'Ready for Delivery' => ['Completed'],
    'Completed' => [],
    'Cancelled' => [],
];
$nextOptions = $transitions[$order['status']] ?? [];
?>

<h1 class="h3 mb-3">Order #<?= (int)$order['order_id'] ?></h1>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <h2 class="h6">Customer</h2>
        <p class="mb-1"><?= e($order['username']) ?> (<?= e($order['email']) ?>)</p>
        <h2 class="h6 mt-3">Delivery</h2>
        <p class="mb-1"><?= e($order['full_name']) ?> &middot; <?= e($order['contact_number']) ?></p>
        <p class="mb-1"><?= e($order['delivery_address']) ?></p>
        <p class="mb-1">Payment: <?= e($order['payment_method']) ?></p>
    </div>
    <div class="col-md-6">
        <h2 class="h6">Status</h2>
        <p><span class="badge bg-secondary fs-6"><?= e($order['status']) ?></span></p>
        <?php if ($nextOptions): ?>
            <form method="post" action="<?= BASE_URL ?>/admin/order_status_update.php" class="d-flex gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="order_id" value="<?= (int)$order['order_id'] ?>">
                <select name="status" class="form-select w-auto">
                    <?php foreach ($nextOptions as $opt): ?>
                        <option value="<?= e($opt) ?>"><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        <?php else: ?>
            <p class="text-muted">This order is in a final state.</p>
        <?php endif; ?>
    </div>
</div>

<table class="table">
    <thead><tr><th>Product</th><th>Customization</th><th>Unit Price</th><th>Qty</th><th>Line Total</th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['product_name_snapshot']) ?></td>
                <td class="small text-muted">
                    <?php if ($item['shape_name_snapshot']): ?>Shape: <?= e($item['shape_name_snapshot']) ?><br><?php endif; ?>
                    <?php if ($item['design_name_snapshot']): ?>Design: <?= e($item['design_name_snapshot']) ?><br><?php endif; ?>
                    <?php if ($item['custom_text']): ?>Text: "<?= e($item['custom_text']) ?>"<br><?php endif; ?>
                    <?php if ($item['custom_image_path']): ?>
                        <img src="<?= BASE_URL ?>/uploads/customizations/<?= e($item['custom_image_path']) ?>" width="40" class="rounded mt-1">
                    <?php endif; ?>
                </td>
                <td><?= format_price($item['unit_price']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td><?= format_price($item['line_total']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="4" class="text-end">Total</th><th><?= format_price($order['total_amount']) ?></th></tr>
    </tfoot>
</table>

<a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline-secondary">Back to Orders</a>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
