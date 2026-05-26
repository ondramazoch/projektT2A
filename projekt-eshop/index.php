<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$productRepo = new ProductRepository();
$featuredProducts = $productRepo->getFeatured(6);
?>
<?php require __DIR__ . '/partials/header.php'; ?>

    <div class="slevy">
        <h2>-- EXTRA SLEVY --</h2>
    </div>

    <section class="products">
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card-sale">
                    <a href="produkt.php?slug=<?= htmlspecialchars($product->slug) ?>" class="product-image">
                        <img src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                    </a>
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product->name) ?></h3>
                        <p class="product-description"><?= htmlspecialchars($product->description) ?></p>
                        <div class="product-bottom">
                            <span class="product-price"><?= number_format($product->price, 0, ',', ' ') ?> Kč</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php require __DIR__ . '/partials/footer.php'; ?>