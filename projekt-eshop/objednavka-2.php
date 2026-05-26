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

$shippingRepo = new ShippingMethodRepository();
$paymentRepo = new PaymentMethodRepository();
$shippingMethods = $shippingRepo->getAll();
$paymentMethods = $paymentRepo->getAll();

$v = new Validator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v->in('shipping', (int) ($_POST['shipping'] ?? 0), array_map(fn($s) => $s->id, $shippingMethods), 'Vyberte způsob dopravy.')
      ->in('payment', (int) ($_POST['payment'] ?? 0), array_map(fn($p) => $p->id, $paymentMethods), 'Vyberte způsob platby.');

    if ($v->isValid()) {
        $_SESSION['order_shipping'] = (int) $_POST['shipping'];
        $_SESSION['order_payment'] = (int) $_POST['payment'];
        header('Location: objednavka-3.php');
        exit;
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="cart-container">
    <section class="steps">
        <ul class="steps-list">
            <span>1. Dodací údaje</span>
            <span class="active">2. Doprava a platba</span>
            <span>3. Shrnutí</span>
        </ul>
    </section>

    <h2 class="napis">Doprava a platba</h2>

    <form class="checkout-form" method="post">
        <h3>Způsob dopravy</h3>
        <?php if ($v->hasError('shipping')): ?>
            <span class="error"><?= htmlspecialchars($v->getError('shipping')) ?></span>
        <?php endif; ?>

        <div class="shipping-options">
            <?php foreach ($shippingMethods as $method): ?>
                <label class="shipping-card">
                    <input type="radio" name="shipping" value="<?= $method->id ?>"
                        <?= isset($_POST['shipping']) && $_POST['shipping'] == $method->id ? 'checked' : '' ?>>
                    <div class="card-content">
                        <span class="shipping-name"><?= htmlspecialchars($method->name) ?></span>
                        <span class="shipping-price">
                            <?= $method->isFree() ? 'Zdarma' : number_format($method->price, 0, ',', ' ') . ' Kč' ?>
                        </span>
                        <span><?= htmlspecialchars($method->deliveryDays) ?></span>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <h3>Způsob platby</h3>
        <?php if ($v->hasError('payment')): ?>
            <span class="error"><?= htmlspecialchars($v->getError('payment')) ?></span>
        <?php endif; ?>

        <div class="shipping-options">
            <?php foreach ($paymentMethods as $method): ?>
                <label class="shipping-card">
                    <input type="radio" name="payment" value="<?= $method->id ?>"
                        <?= isset($_POST['payment']) && $_POST['payment'] == $method->id ? 'checked' : '' ?>>
                    <div class="card-content">
                        <span class="shipping-name"><?= htmlspecialchars($method->name) ?></span>
                        <span class="shipping-price">
                            <?= $method->isFree() ? 'Zdarma' : '+' . number_format($method->price, 0, ',', ' ') . ' Kč' ?>
                        </span>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="buttons">
            <button type="submit" class="next">Pokračovat</button>
            <a href="objednavka-1.php" class="cancel">Zpět</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>