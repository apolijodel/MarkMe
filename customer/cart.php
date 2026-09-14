<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$cartId = get_or_create_cart_id(current_user()['user_id']);

$stmt = db()->prepare(
    'SELECT ci.*, p.name AS product_name, p.price, p.stock_quantity, p.image_path AS product_image,
            s.name AS shape_name, d.name AS design_name
     FROM cart_items ci
     JOIN products p ON p.product_id = ci.product_id
     LEFT JOIN shapes s ON s.shape_id = ci.shape_id
     LEFT JOIN designs d ON d.design_id = ci.design_id
     WHERE ci.cart_id = :cart_id
     ORDER BY ci.created_at DESC'
);
$stmt->execute(['cart_id' => $cartId]);
$items = $stmt->fetchAll();

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$pageTitle = 'My Cart';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="h3 mb-4">Shopping Cart</h1>

<?php if (!$items): ?>
    <p class="text-muted">Your cart is empty. <a href="<?= BASE_URL ?>/customer/catalog.php">Browse products</a>.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customization</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= BASE_URL ?>/uploads/products/<?= e($item['product_image']) ?>" width="50" height="50" style="object-fit:cover" class="rounded">
                                <?= e($item['product_name']) ?>
                            </div>
                        </td>
                        <td class="small text-muted">
                            <?php if ($item['shape_name']): ?>Shape: <?= e($item['shape_name']) ?><br><?php endif; ?>
                            <?php if ($item['design_name']): ?>Design: <?= e($item['design_name']) ?><br><?php endif; ?>
                            <?php if ($item['custom_text']): ?>Text: "<?= e($item['custom_text']) ?>"<br><?php endif; ?>
                            <?php if ($item['custom_image_path']): ?>
                                <img src="<?= BASE_URL ?>/uploads/customizations/<?= e($item['custom_image_path']) ?>" width="40" class="rounded mt-1">
                            <?php endif; ?>
                        </td>
                        <td><?= format_price($item['price']) ?></td>
                        <td style="max-width:110px;">
                            <form method="post" action="<?= BASE_URL ?>/customer/update_cart.php" class="d-flex gap-1">
                                <?= csrf_field() ?>
                                <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id'] ?>">
                                <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$item['stock_quantity'] ?>" data-max-stock="<?= (int)$item['stock_quantity'] ?>" class="form-control form-control-sm">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                            </form>
                        </td>
                        <td><?= format_price($item['price'] * $item['quantity']) ?></td>
                        <td>
                            <form method="post" action="<?= BASE_URL ?>/customer/remove_cart_item.php" onsubmit="return confirm('Remove this item?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end align-items-center gap-3">
        <span class="fs-5">Total: <strong><?= format_price($total) ?></strong></span>
        <a href="<?= BASE_URL ?>/customer/checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
