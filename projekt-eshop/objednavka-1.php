<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();

if ($cart->isEmpty()) {
    header('Location: kosik.php');
    exit;
}

$v = new Validator();
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $v->required('name', trim($_POST['name'] ?? ''), 'Jméno je povinné.')
      ->required('surname', trim($_POST['surname'] ?? ''), 'Příjmení je povinné.')
      ->required('email', trim($_POST['email'] ?? ''), 'E-mail je povinný.')
      ->email('email', trim($_POST['email'] ?? ''), 'Neplatný formát e-mailu.')
      ->required('phone', trim($_POST['phone'] ?? ''), 'Telefon je povinný.')
      ->required('street', trim($_POST['street'] ?? ''), 'Ulice je povinná.')
      ->required('city', trim($_POST['city'] ?? ''), 'Město je povinné.')
      ->required('zip', trim($_POST['zip'] ?? ''), 'PSČ je povinné.')
      ->pattern('zip', trim($_POST['zip'] ?? ''), '/^\d{3}\s?\d{2}$/', 'PSČ musí mít 5 číslic.');

    if ($v->isValid()) {
        $_SESSION['order_address'] = [
            'name'    => trim($_POST['name']),
            'surname' => trim($_POST['surname']),
            'email'   => trim($_POST['email']),
            'phone'   => trim($_POST['phone']),
            'street'  => trim($_POST['street']),
            'city'    => trim($_POST['city']),
            'zip'     => trim($_POST['zip']),
            'note'    => trim($_POST['note'] ?? ''),
        ];
        header('Location: objednavka-2.php');
        exit;
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="cart-container">
    <section class="steps">
        <ul class="steps-list">
            <span class="active">1. Dodací údaje</span>
            <span>2. Doprava a platba</span>
            <span>3. Shrnutí</span>
        </ul>
    </section>

    <h2 class="napis">Dodací údaje</h2>

    <form class="checkout-form" method="post">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Jméno *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                <?php if ($v->hasError('name')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('name')) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="surname">Příjmení *</label>
                <input type="text" id="surname" name="surname" value="<?= htmlspecialchars($old['surname'] ?? '') ?>">
                <?php if ($v->hasError('surname')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('surname')) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <?php if ($v->hasError('email')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('email')) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone">Telefon *</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                <?php if ($v->hasError('phone')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('phone')) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="street">Ulice a č.p. *</label>
            <input type="text" id="street" name="street" value="<?= htmlspecialchars($old['street'] ?? '') ?>">
            <?php if ($v->hasError('street')): ?>
                <span class="error"><?= htmlspecialchars($v->getError('street')) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="city">Město *</label>
                <input type="text" id="city" name="city" value="<?= htmlspecialchars($old['city'] ?? '') ?>">
                <?php if ($v->hasError('city')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('city')) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="zip">PSČ *</label>
                <input type="text" id="zip" name="zip" value="<?= htmlspecialchars($old['zip'] ?? '') ?>">
                <?php if ($v->hasError('zip')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('zip')) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="note">Poznámka</label>
            <input type="text" id="note" name="note" value="<?= htmlspecialchars($old['note'] ?? '') ?>">
        </div>

        <div class="buttons">
            <button type="submit" class="next">Pokračovat</button>
            <a href="kosik.php" class="cancel">Zpět</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>