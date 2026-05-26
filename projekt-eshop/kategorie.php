<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$categoryRepo = new CategoryRepository();
$categories = $categoryRepo->getAll();
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="categories">
    <h2 class="section-title">Kategorie</h2>

    <div class="products-grid">
        <?php foreach ($categories as $category): ?>
            <div class="product-card product-card--category">
                <a href="produkty.php?slug=<?= htmlspecialchars($category->slug) ?>" class="category-link">
                    <div class="category-image">
                        <img src="<?= htmlspecialchars($category->image) ?>" alt="<?= htmlspecialchars($category->name) ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($category->name) ?></h3>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>