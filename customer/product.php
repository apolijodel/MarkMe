<?php
require_once __DIR__ . '/../includes/functions.php';

$productId = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare(
    'SELECT p.*, c.name AS category_name FROM products p
     JOIN categories c ON c.category_id = p.category_id
     WHERE p.product_id = :id'
);
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Not Found';
    require __DIR__ . '/../includes/header.php';
    echo '<p>Product not found.</p>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$shapes = $product['is_customizable'] ? db()->query('SELECT * FROM shapes ORDER BY name')->fetchAll() : [];
$designs = $product['is_customizable'] ? db()->query('SELECT * FROM designs ORDER BY name')->fetchAll() : [];
$outOfStock = (int)$product['stock_quantity'] <= 0;

$pageTitle = $product['name'];
require __DIR__ . '/../includes/header.php';
?>

<div class="row g-4">
    <div class="col-md-5">
        <img src="<?= BASE_URL ?>/uploads/products/<?= e($product['image_path']) ?>" class="img-fluid rounded" alt="<?= e($product['name']) ?>">
    </div>
    <div class="col-md-7">
        <span class="badge bg-secondary mb-2"><?= e($product['category_name']) ?></span>
        <h1 class="h3"><?= e($product['name']) ?></h1>
        <p class="fs-4 fw-bold"><?= format_price($product['price']) ?></p>
        <p><?= nl2br(e($product['description'])) ?></p>

        <?php if ($outOfStock): ?>
            <div class="alert alert-danger">This product is currently out of stock.</div>
        <?php else: ?>
            <p class="text-muted">Only <?= (int)$product['stock_quantity'] ?> left in stock.</p>
        <?php endif; ?>

        <?php if (!$outOfStock): ?>
        <form method="post" action="<?= BASE_URL ?>/customer/add_to_cart.php" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">

            <?php if ($product['is_customizable']): ?>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Choose a Shape</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($shapes as $i => $shape): ?>
                            <label class="option-thumb <?= $i === 0 ? 'selected' : '' ?>" style="width:90px;">
                                <input type="radio" name="shape_id" value="<?= (int)$shape['shape_id'] ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                <img src="<?= BASE_URL ?>/uploads/shapes/<?= e($shape['image_path']) ?>" alt="<?= e($shape['name']) ?>">
                                <div class="small text-center"><?= e($shape['name']) ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Choose a Design</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($designs as $i => $design): ?>
                            <label class="option-thumb <?= $i === 0 ? 'selected' : '' ?>" style="width:90px;">
                                <input type="radio" name="design_id" value="<?= (int)$design['design_id'] ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                <img src="<?= BASE_URL ?>/uploads/designs/<?= e($design['image_path']) ?>" alt="<?= e($design['name']) ?>">
                                <div class="small text-center"><?= e($design['name']) ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Your Photo</label>
                    <input type="file" id="custom_image" name="custom_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">JPG, PNG, or WEBP. Max 2MB.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Personalized Text</label>
                    <input type="text" id="custom_text" name="custom_text" class="form-control" maxlength="120" placeholder="A name, quote, or short message">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Preview</label>
                    <div class="preview-stack text-center">
                        <img id="photo-preview" class="d-none rounded" alt="Your uploaded photo">
                        <p class="text-muted mb-1">Shape and design chosen above will be applied to your bookmark.</p>
                        <p id="text-preview" class="fst-italic">(your text will appear here)</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?= (int)$product['stock_quantity'] ?>" data-max-stock="<?= (int)$product['stock_quantity'] ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
