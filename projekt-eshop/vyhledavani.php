<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$productRepo = new ProductRepository();

$query = trim($_GET['q'] ?? '');
$products = [];

if ($query !== '') {
    $products = $productRepo->search($query);
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="categories">
    <?php if ($query === ''): ?>
        <h2 class="section-title">Zadejte hledaný výraz</h2>
    <?php elseif (empty($products)): ?>
        <h2 class="section-title">Žádné výsledky pro "<?= htmlspecialchars($query) ?>"</h2>
        <p style="text-align: center;">Zkuste jiné klíčové slovo nebo <a href="kategorie.php">procházejte kategorie</a>.</p>
    <?php else: ?>
        <h2 class="section-title">Výsledky pro "<?= htmlspecialchars($query) ?>"</h2>
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
    <?php endif; ?>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>