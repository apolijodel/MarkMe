<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/admin_header.php';

$counts = [
    'products' => db()->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'categories' => db()->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'customers' => db()->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'orders' => db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
];

$lowStock = db()->query(
    'SELECT * FROM products WHERE stock_quantity <= 5 ORDER BY stock_quantity ASC LIMIT 5'
)->fetchAll();

$recentOrders = db()->query(
    'SELECT o.*, u.username FROM orders o JOIN users u ON u.user_id = o.user_id
     ORDER BY o.created_at DESC LIMIT 5'
)->fetchAll();

$salesTotal = db()->query(
    "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'Cancelled'"
)->fetchColumn();
?>

<h1 class="h3 mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold"><?= (int)$counts['products'] ?></div>
            <div class="text-muted">Products</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold"><?= (int)$counts['categories'] ?></div>
            <div class="text-muted">Categories</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold"><?= (int)$counts['customers'] ?></div>
            <div class="text-muted">Customers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold"><?= (int)$counts['orders'] ?></div>
            <div class="text-muted">Orders</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <h2 class="h5">Recent Orders</h2>
        <p class="text-muted">Total Sales (excluding cancelled): <strong><?= format_price($salesTotal) ?></strong></p>
        <table class="table table-sm">
            <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><a href="<?= BASE_URL ?>/admin/order_view.php?id=<?= (int)$order['order_id'] ?>">#<?= (int)$order['order_id'] ?></a></td>
                        <td><?= e($order['username']) ?></td>
                        <td><?= format_price($order['total_amount']) ?></td>
                        <td><?= e($order['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentOrders): ?><tr><td colspan="4" class="text-muted">No orders yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        <h2 class="h5">Low Stock Products</h2>
        <table class="table table-sm">
            <thead><tr><th>Product</th><th>Stock</th></tr></thead>
            <tbody>
                <?php foreach ($lowStock as $product): ?>
                    <tr>
                        <td><a href="<?= BASE_URL ?>/admin/product_form.php?id=<?= (int)$product['product_id'] ?>"><?= e($product['name']) ?></a></td>
                        <td><span class="badge bg-<?= $product['stock_quantity'] == 0 ? 'danger' : 'warning' ?>"><?= (int)$product['stock_quantity'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$lowStock): ?><tr><td colspan="2" class="text-muted">All stocked up.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
