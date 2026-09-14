<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>
<nav class="col-md-2 d-md-block bg-white border-end sidebar py-4">
    <ul class="nav flex-column">
        <?php
        $links = [
            'dashboard.php' => 'Dashboard',
            'categories.php' => 'Categories',
            'products.php' => 'Products',
            'shapes.php' => 'Shapes',
            'designs.php' => 'Designs',
            'customers.php' => 'Customers',
            'orders.php' => 'Orders',
        ];
        foreach ($links as $file => $label):
            $active = $current === $file ? ' active fw-semibold' : '';
        ?>
            <li class="nav-item">
                <a class="nav-link<?= $active ?>" href="<?= BASE_URL ?>/admin/<?= $file ?>"><?= e($label) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
