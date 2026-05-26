<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$cart = new Cart();
$cartItemCount = $cart->getTotalQuantity();
$pageTitle = 'O nás – ALL4DISCGOLF';
?>
<?php require __DIR__ . '/partials/header.php'; ?>

<h2 class="section-title">O nás</h2>

<section class="about-us">
    <div class="about-us-content">
        <p>
            Vítejte v našem e-shopu ALL4DISCGOLF, vašem spolehlivém partnerovi pro discgolfové potřeby! Jsme nadšenci do discgolfu a naším cílem je poskytovat kvalitní vybavení a skvělý zákaznický servis pro všechny hráče, od začátečníků po zkušené profesionály.
        </p><br>
        <p>
            Naše nabídka zahrnuje širokou škálu discgolfových disků, tašek, košů a dalšího příslušenství od předních značek v oboru. Pečlivě vybíráme produkty, které splňují nejvyšší standardy kvality a výkonu, abychom vám pomohli dosáhnout vašich discgolfových cílů.
        </p><br>
        <p>
            Věříme v budování komunity kolem discgolfu a rádi sdílíme naši vášeň s našimi zákazníky. Ať už jste nováček nebo zkušený hráč, jsme tu, abychom vám pomohli najít to pravé vybavení a poskytli odborné rady.
        </p><br>
        <p>
            Děkujeme, že jste si vybrali ALL4DISCGOLF jako svého partnera pro discgolfové potřeby. Těšíme se na spolupráci s vámi a přejeme vám mnoho úspěchů na discgolfovém hřišti!
        </p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>