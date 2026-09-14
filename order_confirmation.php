<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$orderId = (int)($_GET['order_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM orders WHERE order_id = :id AND user_id = :uid');
$stmt->execute(['id' => $orderId, 'uid' => current_user()['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    $pageTitle = 'Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<p>Order not found.</p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Order Confirmed';
require __DIR__ . '/includes/header.php';
?>

<div class="text-center py-5">
    <h1 class="h3 text-success mb-3">Thank you! Your order has been placed.</h1>
    <p class="fs-5">Order #<?= (int)$order['order_id'] ?> &middot; Total: <?= format_price($order['total_amount']) ?></p>
    <p class="text-muted">Payment Method: <?= e($order['payment_method']) ?> &middot; Status: <?= e($order['status']) ?></p>
    <div class="mt-4">
        <a href="<?= BASE_URL ?>/order_detail.php?id=<?= (int)$order['order_id'] ?>" class="btn btn-primary">View Order Details</a>
        <a href="<?= BASE_URL ?>/catalog.php" class="btn btn-outline-secondary">Continue Shopping</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
