<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/products.php');
}
csrf_check();

$productId = (int)($_POST['product_id'] ?? 0);

try {
    db()->prepare('DELETE FROM products WHERE product_id = :id')->execute(['id' => $productId]);
    flash_set('success', 'Product deleted.');
} catch (PDOException $e) {
    flash_set('error', 'Cannot delete this product because it appears in existing cart or order records.');
}

redirect('/admin/products.php');
