<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();

if ($cart->isEmpty()) {
    header('Location: kosik.php');
    exit;
}

if (!isset($_SESSION['order_address']) || !isset($_SESSION['order_shipping'])) {
    header('Location: objednavka-1.php');
    exit;
}

$customerRepo = new CustomerRepository();
$orderRepo = new OrderRepository();
$address = $_SESSION['order_address'];

// Vytvoření zákazníka
$customer = $customerRepo->create(
    firstName: $address['name'],
    lastName: $address['surname'],
    email: $address['email'],
    phone: $address['phone'],
    street: $address['street'],
    city: $address['city'],
    zip: $address['zip'],
);

// Vytvoření objednávky
$order = $orderRepo->create(
    customerId: $customer->id,
    shippingMethodId: $_SESSION['order_shipping'],
    paymentMethodId: $_SESSION['order_payment'],
    note: $address['note'],
    cartItems: $cart->getItems(),
);

// Vyprázdnění košíku a session
$cart->clear();
unset($_SESSION['order_address']);
unset($_SESSION['order_shipping']);
unset($_SESSION['order_payment']);

$cartItemCount = 0;
$pageTitle = 'Potvrzení objednávky – ALL4DISCGOLF';
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="cart-container">
    <section class="order-confirmation">
        <div class="confirmation-content">
            <div class="success-icon">✓</div>

            <h2 class="napis">Objednávka byla úspěšně odeslána!</h2>

            <p class="conf-text">
                Děkujeme za váš nákup v <strong>ALL4DISCGOLF</strong>.<br>
                Potvrzení objednávky jsme vám odeslali na e-mail.
            </p>

            <div class="order-number-box">
                <span>Číslo vaší objednávky:</span>
                <strong>#<?= $order->id ?></strong>
            </div>

            <div class="buttons">
                <a href="index.php" class="next">Zpět do e-shopu</a>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>