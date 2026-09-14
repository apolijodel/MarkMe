</main>
<footer class="site-footer pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="brand-mark mb-3">MarkMe</div>
                <p class="small">Design your own bookmark — pick a shape, a design, add your photo and a
                    personal message, and we'll craft it just for you.</p>
            </div>
            <div class="col-6 col-md-3">
                <h6>Explore</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2">
                    <li><a href="<?= BASE_URL ?>/customer/index.php">Home</a></li>
                    <li><a href="<?= BASE_URL ?>/customer/index.php#story">Our Story</a></li>
                    <li><a href="<?= BASE_URL ?>/customer/index.php#offer">What We Offer</a></li>
                    <li><a href="<?= BASE_URL ?>/customer/index.php#how-it-works">How It Works</a></li>
                    <li><a href="<?= BASE_URL ?>/customer/index.php#products">Shop</a></li>
                    <li><a href="<?= BASE_URL ?>/customer/index.php#contact">Contact</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <h6>Account</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2">
                    <?php if (is_logged_in()): ?>
                        <li><a href="<?= BASE_URL ?>/customer/orders.php">My Orders</a></li>
                        <li><a href="<?= BASE_URL ?>/customer/profile.php">Profile</a></li>
                    <?php else: ?>
                        <li><button type="button" class="btn btn-link p-0 text-start" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button></li>
                        <li><button type="button" class="btn btn-link p-0 text-start" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>Payment</h6>
                <p class="small mb-0">Cash on Delivery or manual Bank Transfer — no online payment gateway required.</p>
            </div>
        </div>
        <div class="footer-bottom text-center">
            &copy; <?= date('Y') ?> MarkMe — Customizable Bookmark E-Commerce System
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!is_logged_in()): require __DIR__ . '/auth_modals.php'; endif; ?>
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>
</html>
