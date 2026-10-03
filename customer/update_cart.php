<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/customer/cart.php');
}
csrf_check();

$cartItemId = (int)($_POST['cart_item_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));
$cartId = get_or_create_cart_id(current_user()['user_id']);

$stmt = db()->prepare(
    'SELECT ci.cart_item_id, p.stock_quantity FROM cart_items ci
     JOIN products p ON p.product_id = ci.product_id
     WHERE ci.cart_item_id = :id AND ci.cart_id = :cart_id'
);
$stmt->execute(['id' => $cartItemId, 'cart_id' => $cartId]);
$row = $stmt->fetch();

if (!$row) {
    if (is_ajax()) {
        json_response(['ok' => false, 'message' => 'Cart item not found.'], 404);
    }
    flash_set('error', 'Cart item not found.');
    redirect('/customer/cart.php');
}

$capped = false;
if ($quantity > (int)$row['stock_quantity']) {
    $quantity = max(1, (int)$row['stock_quantity']);
    $capped = true;
}

db()->prepare('UPDATE cart_items SET quantity = :qty WHERE cart_item_id = :id')
    ->execute(['qty' => $quantity, 'id' => $cartItemId]);

if (is_ajax()) {
    json_response([
        'ok' => true,
        'message' => $capped ? 'Quantity reduced to match stock' : 'Cart updated',
        'count' => cart_item_count(),
    ]);
}

if ($capped) {
    flash_set('error', 'Quantity was reduced to match available stock.');
}
redirect('/customer/cart.php');
