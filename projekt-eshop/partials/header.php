<?php
$pageTitle ??= 'ALL4DISCGOLF';
$cartItemCount ??= 0;
?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/main.css">
  <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <header>
        <a href="index.php">
            <img src="assets/images/logo.png" alt="Logo E-shopu" class="logo">
        </a>

        <nav class="header-nav">
          <form class="search-form" action="vyhledavani.php" method="get">
            <input
                type="search"
                name="q"
                placeholder="Hledat produkty..."
                class="search-input"
                >
            <button type="submit" class="search-btn">🔍</button>
          </form>
          <a href="index.php" class="hnav">Domů 🏠</a>
          <a href="kosik.php" class="hnav">Košík 🛒 (<?= $cartItemCount ?>)</a>
        </nav>
    </header>

    <nav class="nav">
        <ul class="nav-list">
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="kategorie.php">Kategorie</a></li>
            <li><a href="o-nas.php">O nás</a></li>
        </ul>
    </nav>