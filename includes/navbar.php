<nav class="navbar navbar-expand-lg site-nav sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/customer/index.php">MarkMe</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/index.php#products">Shop</a></li>
            </ul>
            <ul class="navbar-nav align-items-lg-center">
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/profile.php">Profile</a></li>
                    <?php if (is_admin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/logout.php">Logout (<?= e(current_user()['username']) ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn-nav-register" href="<?= BASE_URL ?>/customer/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
