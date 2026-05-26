<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();

// Přidání do košíku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $variant = '';
    if (isset($_POST['variants']) && is_array($_POST['variants'])) {
        $parts = [];
        foreach ($_POST['variants'] as $name => $value) {
            $parts[] = $name . ': ' . $value;
        }
        $variant = implode(', ', $parts);
    }

    $cart->add(
        productId: (int) $_POST['product_id'],
        productName: $_POST['product_name'],
        unitPrice: (float) $_POST['product_price'],
        image: $_POST['product_image'],
        quantity: (int) ($_POST['quantity'] ?? 1),
        variant: $variant,
    );

    header('Location: kosik.php');
    exit;
}

// Odebrání položky
if (isset($_GET['remove'])) {
    $cart->remove((int) $_GET['remove'], $_GET['variant'] ?? '');
    header('Location: kosik.php');
    exit;
}

$items = $cart->getItems();
$total = $cart->getTotalPrice();
$cartItemCount = $cart->getTotalQuantity();
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="cart-container">
    <h2 class="napis">Váš košík</h2>

    <?php if ($cart->isEmpty()): ?>
        <p style="text-align: center;">Košík je prázdný. <a href="kategorie.php">Pokračovat v nákupu</a></p>
    <?php else: ?>
        <section class="product-summary">
            <div class="product-list">
                <div class="cart-main">
                    <?php foreach ($items as $item): ?>
                        <div class="product-card-small">
                            <div class="product-image-small">
                                <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->productName) ?>">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title"><?= htmlspecialchars($item->productName) ?></h3>
                                <?php if ($item->variant !== ''): ?>
                                    <p><?= htmlspecialchars($item->variant) ?></p>
                                <?php endif; ?>
                                <div class="product-bottom">
                                    <span class="product-price"><?= number_format($item->unitPrice, 0, ',', ' ') ?> Kč</span>
                                </div>
                            </div>
                            <div class="quantity-selector">
                                <label>Množství: <?= $item->quantity ?></label>
                            </div>
                            <a href="kosik.php?remove=<?= $item->productId ?>&variant=<?= urlencode($item->variant) ?>" class="cancel" title="Odebrat">✕</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="order-summary">
                <h3>Shrnutí</h3>
                <div class="summary-details">
                    <p>Počet položek: <?= $cartItemCount ?></p>
                    <hr>
                    <p><strong>Celkem: <?= number_format($total, 0, ',', ' ') ?> Kč</strong></p>
                </div>
            </aside>

            <div class="buttons">
                <a href="objednavka-1.php" class="next">Pokračovat</a>
                <a href="kategorie.php" class="cancel">Pokračovat v nákupu</a>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>