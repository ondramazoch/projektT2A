<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();

if ($cart->isEmpty()) {
    header('Location: kosik.php');
    exit;
}

if (!isset($_SESSION['order_address'])) {
    header('Location: objednavka-1.php');
    exit;
}

if (!isset($_SESSION['order_shipping'])) {
    header('Location: objednavka-2.php');
    exit;
}

$shippingRepo = new ShippingMethodRepository();
$paymentRepo = new PaymentMethodRepository();
$shipping = $shippingRepo->getById($_SESSION['order_shipping']);
$payment = $paymentRepo->getById($_SESSION['order_payment']);
$address = $_SESSION['order_address'];
$items = $cart->getItems();
$itemsTotal = $cart->getTotalPrice();
$total = $itemsTotal + $shipping->price + $payment->price;

$v = new Validator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v->required('terms', $_POST['terms'] ?? '', 'Musíte souhlasit s obchodními podmínkami.');

    if ($v->isValid()) {
        header('Location: objednavka-potvrzeni.php');
        exit;
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="cart-container">
    <section class="steps">
        <ul class="steps-list">
            <span>1. Dodací údaje</span>
            <span>2. Doprava a platba</span>
            <span class="active">3. Shrnutí</span>
        </ul>
    </section>

    <h2 class="napis">Shrnutí objednávky</h2>

    <section class="product-summary">
        <div class="product-list">

            <h3>Dodací údaje</h3>
            <p><?= htmlspecialchars($address['name']) ?> <?= htmlspecialchars($address['surname']) ?></p>
            <p><?= htmlspecialchars($address['email']) ?></p>
            <p><?= htmlspecialchars($address['phone']) ?></p>
            <p><?= htmlspecialchars($address['street']) ?>, <?= htmlspecialchars($address['zip']) ?> <?= htmlspecialchars($address['city']) ?></p>
            <?php if ($address['note'] !== ''): ?>
                <p>Poznámka: <?= htmlspecialchars($address['note']) ?></p>
            <?php endif; ?>

            <h3>Doprava a platba</h3>
            <p>Doprava: <?= htmlspecialchars($shipping->name) ?> – <?= $shipping->isFree() ? 'Zdarma' : number_format($shipping->price, 0, ',', ' ') . ' Kč' ?></p>
            <p>Platba: <?= htmlspecialchars($payment->name) ?> – <?= $payment->isFree() ? 'Zdarma' : number_format($payment->price, 0, ',', ' ') . ' Kč' ?></p>

            <h3>Položky</h3>
            <?php foreach ($items as $item): ?>
                <div class="product-card-small">
                    <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->productName) ?>" style="width:80px;">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($item->productName) ?></h3>
                        <?php if ($item->variant !== ''): ?>
                            <p><?= htmlspecialchars($item->variant) ?></p>
                        <?php endif; ?>
                        <p>Množství: <?= $item->quantity ?> × <?= number_format($item->unitPrice, 0, ',', ' ') ?> Kč</p>
                    </div>
                </div>
            <?php endforeach; ?>

            <form method="post">
                <?php if ($v->hasError('terms')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('terms')) ?></span>
                <?php endif; ?>
                <label>
                    <input type="checkbox" name="terms" value="1">
                    Souhlasím s obchodními podmínkami
                </label>

                <div class="buttons">
                    <button type="submit" class="next">Odeslat objednávku</button>
                    <a href="objednavka-2.php" class="cancel">Zpět</a>
                </div>
            </form>
        </div>

        <aside class="order-summary">
            <h3>Celková cena</h3>
            <p>Zboží: <?= number_format($itemsTotal, 0, ',', ' ') ?> Kč</p>
            <p>Doprava: <?= $shipping->isFree() ? 'Zdarma' : number_format($shipping->price, 0, ',', ' ') . ' Kč' ?></p>
            <p>Platba: <?= $payment->isFree() ? 'Zdarma' : number_format($payment->price, 0, ',', ' ') . ' Kč' ?></p>
            <hr>
            <p><strong>Celkem: <?= number_format($total, 0, ',', ' ') ?> Kč</strong></p>
        </aside>
    </section>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>