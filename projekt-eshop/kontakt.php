<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$pageTitle = 'Kontakt – ALL4DISCGOLF';

$v = new Validator();
$sent = false;
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $v->required('name', trim($_POST['name'] ?? ''), 'Jméno je povinné.')
      ->required('email', trim($_POST['email'] ?? ''), 'E-mail je povinný.')
      ->email('email', trim($_POST['email'] ?? ''), 'Neplatný formát e-mailu.')
      ->required('message', trim($_POST['message'] ?? ''), 'Zpráva je povinná.');

    if ($v->isValid()) {
        $sent = true;
        $old = [];
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<h2 class="section-title">Kontakt</h2>

<div class="about-us">
    <?php if ($sent): ?>
        <p style="text-align: center; color: green; font-size: 1.2rem;">✓ Zpráva byla odeslána!</p>
    <?php endif; ?>

    <form class="kontakt-form" method="post">
        <div class="kontakt-row">
            <div class="form-group">
                <label for="name">Jméno:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                <?php if ($v->hasError('name')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('name')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <?php if ($v->hasError('email')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('email')) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="kontakt-message">
            <div class="form-group">
                <label for="message">Zpráva:</label>
                <textarea id="message" name="message" rows="5"><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
                <?php if ($v->hasError('message')): ?>
                    <span class="error"><?= htmlspecialchars($v->getError('message')) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="buttons">
            <button type="submit" class="next">Odeslat</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>