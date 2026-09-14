<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$user = current_user();
$cartId = get_or_create_cart_id($user['user_id']);

$stmt = db()->prepare(
    'SELECT ci.*, p.name AS product_name, p.price, p.stock_quantity,
            s.name AS shape_name, d.name AS design_name
     FROM cart_items ci
     JOIN products p ON p.product_id = ci.product_id
     LEFT JOIN shapes s ON s.shape_id = ci.shape_id
     LEFT JOIN designs d ON d.design_id = ci.design_id
     WHERE ci.cart_id = :cart_id'
);
$stmt->execute(['cart_id' => $cartId]);
$items = $stmt->fetchAll();

if (!$items) {
    flash_set('error', 'Your cart is empty.');
    redirect('/customer/cart.php');
}

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$pageTitle = 'Checkout';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="h3 mb-4">Checkout</h1>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card p-3 p-md-4">
        <h2 class="h5">Order Summary</h2>
        <table class="table">
            <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?= e($item['product_name']) ?>
                            <?php if ($item['shape_name'] || $item['design_name']): ?>
                                <div class="small text-muted">
                                    <?= e($item['shape_name']) ?><?= $item['shape_name'] && $item['design_name'] ? ' / ' : '' ?><?= e($item['design_name']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td><?= format_price($item['price']) ?></td>
                        <td><?= format_price($item['price'] * $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><th colspan="3" class="text-end">Total</th><th><?= format_price($total) ?></th></tr>
            </tfoot>
        </table>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card p-3 p-md-4">
        <h2 class="h5">Delivery & Payment</h2>
        <form method="post" action="<?= BASE_URL ?>/customer/place_order.php">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="<?= e($user['contact_number']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Delivery Address</label>
                <textarea name="delivery_address" class="form-control" rows="3" required><?= e($user['address']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select">
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="Bank Transfer">Bank Transfer (manual)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">Place Order</button>
        </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
