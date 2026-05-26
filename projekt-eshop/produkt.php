<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$productRepo = new ProductRepository();

$slug = trim($_GET['slug'] ?? '');
$product = $slug !== '' ? $productRepo->getBySlug($slug) : null;

if ($product === null) {
    header('Location: 404.php');
    exit;
}

$params = $productRepo->getParameters($product->id);
$selectableParams = array_filter($params, fn($p) => $p->isSelectable());

$pageTitle = $product->name . ' – ALL4DISCGOLF';
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="product-detail-container">
    <div class="product-detail-main">

        <div class="product-detail-image">
            <img src="<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>">
        </div>

        <div class="product-detail-info">
            <h1 class="product-detail-title"><?= htmlspecialchars($product->name) ?></h1>

            <p class="product-detail-price">
                <?= number_format($product->price, 0, ',', ' ') ?> Kč
                <?php if ($product->hasDiscount()): ?>
                    <span style="text-decoration: line-through; color: gray; font-size: 1.2rem;">
                        <?= number_format($product->originalPrice, 0, ',', ' ') ?> Kč
                    </span>
                <?php endif; ?>
            </p>

            <div class="product-detail-description">
                <h3>Popis produktu</h3>
                <p><?= htmlspecialchars($product->description) ?></p>
            </div>

            <div class="product-specs">
                <form class="specs" method="post" action="kosik.php">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product->name) ?>">
                    <input type="hidden" name="product_price" value="<?= $product->price ?>">
                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($product->image) ?>">

                    <?php foreach ($selectableParams as $param): ?>
                        <label for="param-<?= htmlspecialchars($param->name) ?>"><?= htmlspecialchars($param->name) ?>:</label>
                        <select id="param-<?= htmlspecialchars($param->name) ?>" name="variants[<?= htmlspecialchars($param->name) ?>]">
                            <?php foreach ($param->getOptions() as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endforeach; ?>

                    <div class="product-detail-actions">
                        <div class="quantity-selector">
                            <label for="qty">Množství:</label>
                            <input type="number" id="qty" name="quantity" value="1" min="1">
                        </div>
                        <button type="submit" class="add-to-cart-btn">Přidat do košíku 🛒</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>