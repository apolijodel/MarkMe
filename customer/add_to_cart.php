<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/customer/index.php');
}
csrf_check();

$productId = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

$stmt = db()->prepare('SELECT * FROM products WHERE product_id = :id');
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    flash_set('error', 'Product not found.');
    redirect('/customer/index.php');
}

if ($quantity > (int)$product['stock_quantity']) {
    flash_set('error', 'Requested quantity exceeds available stock.');
    redirect('/customer/product.php?id=' . $productId);
}

$shapeId = null;
$designId = null;
$customText = null;
$customImagePath = null;

if ($product['is_customizable']) {
    $shapeId = !empty($_POST['shape_id']) ? (int)$_POST['shape_id'] : null;
    $designId = !empty($_POST['design_id']) ? (int)$_POST['design_id'] : null;
    $customText = trim($_POST['custom_text'] ?? '') ?: null;

    if (!empty($_FILES['custom_image']['name'])) {
        try {
            $customImagePath = handle_image_upload($_FILES['custom_image'], __DIR__ . '/uploads/customizations');
        } catch (RuntimeException $e) {
            flash_set('error', $e->getMessage());
            redirect('/customer/product.php?id=' . $productId);
        }
    }
}

$cartId = get_or_create_cart_id(current_user()['user_id']);

$stmt = db()->prepare(
    'INSERT INTO cart_items (cart_id, product_id, quantity, shape_id, design_id, custom_text, custom_image_path)
     VALUES (:cart_id, :product_id, :quantity, :shape_id, :design_id, :custom_text, :custom_image_path)'
);
$stmt->execute([
    'cart_id' => $cartId,
    'product_id' => $productId,
    'quantity' => $quantity,
    'shape_id' => $shapeId,
    'design_id' => $designId,
    'custom_text' => $customText,
    'custom_image_path' => $customImagePath,
]);

flash_set('success', 'Added to cart.');
redirect('/customer/cart.php');
