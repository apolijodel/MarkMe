<?php
require_once __DIR__ . '/../includes/functions.php';

$featured = db()->query(
    'SELECT * FROM products ORDER BY created_at DESC LIMIT 3'
)->fetchAll();

$pageTitle = 'Home';
$fullWidth = true;
require __DIR__ . '/../includes/header.php';
?>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="eyebrow">Handmade &middot; Personalized &middot; Yours</div>
                <h1>A bookmark as unique as your story.</h1>
                <p class="lead">Pick a shape, choose a design, upload your favorite photo, and add a
                    name or quote. MarkMe turns a simple bookmark into something worth keeping.</p>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    <a href="<?= BASE_URL ?>/customer/catalog.php" class="btn btn-primary btn-lg">Shop Now</a>
                    <a href="#how-it-works" class="btn btn-outline-primary btn-lg">See How It Works</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="bookmark-shape bm-1"></div>
                    <div class="bookmark-shape bm-2"></div>
                    <div class="bookmark-shape bm-3"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="story-media p-4">
                    <div class="bookmark-shape" style="position:static;width:70px;height:170px;background:var(--gold);"></div>
                    <div class="bookmark-shape" style="position:static;width:70px;height:170px;background:var(--accent);"></div>
                    <div class="bookmark-shape" style="position:static;width:70px;height:170px;background:var(--cream);"></div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="eyebrow">Our Story</div>
                <h2 class="mt-2 mb-3">Made for readers, gift-givers, and everyone in between.</h2>
                <p class="story-quote">"We started MarkMe because every book someone loves deserves a bookmark
                    that means something too."</p>
                <p class="text-body-secondary">What began as a small idea for handmade class gifts grew into a
                    platform where students, teachers, readers, and stationery lovers can design a bookmark
                    that's genuinely theirs — a name, a favorite quote, a cherished photo, all on a shape and
                    design they picked themselves.</p>
            </div>
        </div>
    </div>
</section>

<!-- What We Offer -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <div class="eyebrow">What We Offer</div>
            <h2 class="mt-2">Everything you need to make it personal</h2>
            <p>Four simple choices, one bookmark that's completely yours.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="9" height="9" rx="2"></rect><circle cx="16.5" cy="16.5" r="4.5"></circle></svg>
                    </div>
                    <h3>Choose a Shape</h3>
                    <p>Rectangular, rounded, heart, or tasseled — pick the silhouette that fits your style.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21c-4.97 0-9-3.58-9-8s4.03-8 9-8 9 3.13 9 7c0 2.5-2 4-4.5 4H14a1.5 1.5 0 0 0-1 2.6c.3.3.5.7.5 1.15 0 .8-.7 1.25-1.5 1.25Z"></path><circle cx="7.5" cy="10.5" r="1"></circle><circle cx="11" cy="7.5" r="1"></circle><circle cx="15.5" cy="8.5" r="1"></circle></svg>
                    </div>
                    <h3>Pick a Design</h3>
                    <p>Floral, minimalist, starry night, or plain kraft — a base look to build on.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="14" rx="2"></rect><path d="m3 14 4.5-4.5a2 2 0 0 1 2.8 0L15 14"></path><path d="m13 12 1.5-1.5a2 2 0 0 1 2.8 0L21 14"></path><path d="M12 21v-4"></path><path d="m9.5 19 2.5-2.5 2.5 2.5"></path></svg>
                    </div>
                    <h3>Upload Your Photo</h3>
                    <p>Add a favorite picture — JPG, PNG, or WEBP, up to 2MB.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"></path><path d="M13.5 6.5l3 3"></path></svg>
                    </div>
                    <h3>Add Personalized Text</h3>
                    <p>A name, a quote, or a short dedication — make it unmistakably yours.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section" id="how-it-works">
    <div class="container">
        <div class="section-title">
            <div class="eyebrow">How It Works</div>
            <h2 class="mt-2">From idea to order in three steps</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Personalize</h3>
                    <p>Choose a product, pick a shape and design, upload a photo, and add your text.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Preview</h3>
                    <p>Check your shape, design, photo, and text together before adding to cart.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Order</h3>
                    <p>Checkout with Cash on Delivery or bank transfer, then track your order status.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <div class="eyebrow">Products</div>
            <h2 class="mt-2">Featured Bookmarks</h2>
            <p>A few favorites to get you started.</p>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($featured as $product): ?>
                <div class="col">
                    <div class="card h-100 product-card">
                        <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['image_path']) ?>" class="card-img-top" alt="<?= e($product['name']) ?>">
                        <div class="card-body">
                            <h3 class="h6 card-title"><?= e($product['name']) ?></h3>
                            <p class="card-text fw-semibold"><?= format_price($product['price']) ?></p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="<?= BASE_URL ?>/customer/product.php?id=<?= (int)$product['product_id'] ?>" class="btn btn-outline-primary btn-sm w-100">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/customer/catalog.php" class="btn btn-outline-primary">Browse All Products</a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section">
    <div class="container">
        <div class="cta-banner">
            <h2>Ready to design your own bookmark?</h2>
            <p class="mb-4">It only takes a minute to personalize one that's truly yours.</p>
            <a href="<?= BASE_URL ?>/customer/catalog.php" class="btn btn-primary btn-lg">Start Designing</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
