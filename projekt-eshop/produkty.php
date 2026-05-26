<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$categoryRepo = new CategoryRepository();
$productRepo = new ProductRepository();

$slug = trim($_GET['slug'] ?? '');
$category = $slug !== '' ? $categoryRepo->getBySlug($slug) : null;

if ($category === null) {
    header('Location: 404.php');
    exit;
}

$products = $productRepo->getByCategorySlug($slug);
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="categories">
    <h2 class="section-title"><?= htmlspecialchars($category->name) ?></h2>

    <section class="products">
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
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
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>