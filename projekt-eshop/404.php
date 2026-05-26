<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$pageTitle = '404 – Stránka nenalezena';

http_response_code(404);
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="cart-container">
    <section class="order-confirmation">
        <div class="confirmation-content">

            <h2 class="napis">Tady nic není... 🙁</h2>

            <p class="conf-text">
                Hledaný obsah nebyl nalezen. Zkuste prosím jiné klíčové slovo nebo se vraťte na domovskou stránku.
            </p>

            <div class="buttons">
                <a href="index.php" class="next">Zpět do e-shopu</a>
            </div>
        </div>
    </section>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>