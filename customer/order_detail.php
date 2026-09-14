<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$orderId = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM orders WHERE order_id = :id AND user_id = :uid');
$stmt->execute(['id' => $orderId, 'uid' => current_user()['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    $pageTitle = 'Not Found';
    require __DIR__ . '/../includes/header.php';
    echo '<p>Order not found.</p>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$itemStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = :id');
$itemStmt->execute(['id' => $orderId]);
$items = $itemStmt->fetchAll();

$pageTitle = 'Order #' . $order['order_id'];
require __DIR__ . '/../includes/header.php';
?>

<h1 class="h3 mb-3">Order #<?= (int)$order['order_id'] ?></h1>
<p class="text-muted">Placed on <?= e(date('M j, Y g:i A', strtotime($order['created_at']))) ?> &middot; Status: <strong><?= e($order['status']) ?></strong></p>

<div class="card p-3 p-md-4 mb-4">
    <h2 class="h6">Delivery Information</h2>
    <p class="mb-1"><?= e($order['full_name']) ?></p>
    <p class="mb-1"><?= e($order['contact_number']) ?></p>
    <p class="mb-1"><?= e($order['delivery_address']) ?></p>
    <p class="mb-0">Payment: <?= e($order['payment_method']) ?></p>
</div>

<div class="card p-3 p-md-4">
<table class="table">
    <thead><tr><th>Product</th><th>Customization</th><th>Unit Price</th><th>Qty</th><th>Line Total</th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['product_name_snapshot']) ?></td>
                <td class="small text-muted">
                    <?php if ($item['shape_name_snapshot']): ?>Shape: <?= e($item['shape_name_snapshot']) ?><br><?php endif; ?>
                    <?php if ($item['design_name_snapshot']): ?>Design: <?= e($item['design_name_snapshot']) ?><br><?php endif; ?>
                    <?php if ($item['custom_text']): ?>Text: "<?= e($item['custom_text']) ?>"<?php endif; ?>
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
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
