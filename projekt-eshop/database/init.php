<?php

declare(strict_types=1);

/**
 * Inicializace databáze – vytvoří tabulky a naplní vzorovými daty.
 *
 * Spuštění: php projekt/database/init.php
 *
 * POZOR: Smaže existující databázi a vytvoří novou!
 */

$dbPath = __DIR__ . '/eshop.db';

// Smazat existující databázi
if (file_exists($dbPath)) {
	unlink($dbPath);
	echo "Stará databáze smazána.\n";
}

$db = new PDO('sqlite:' . $dbPath, options: [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$db->exec('PRAGMA journal_mode = WAL');
$db->exec('PRAGMA foreign_keys = ON');

// ============================================================
// Vytvoření tabulek
// ============================================================

$db->exec('
    CREATE TABLE categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        image TEXT NOT NULL DEFAULT "",
        description TEXT NOT NULL DEFAULT ""
    )
');

$db->exec('
    CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        price REAL NOT NULL,
        original_price REAL,
        description TEXT NOT NULL DEFAULT "",
        image TEXT NOT NULL DEFAULT "",
        featured INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )
');

$db->exec('
    CREATE TABLE product_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        image TEXT NOT NULL,
        sort_order INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )
');

$db->exec('
    CREATE TABLE product_parameters (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        value TEXT NOT NULL,
        type TEXT NOT NULL DEFAULT "info",
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )
');

$db->exec('
    CREATE TABLE customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL DEFAULT "",
        street TEXT NOT NULL DEFAULT "",
        city TEXT NOT NULL DEFAULT "",
        zip TEXT NOT NULL DEFAULT "",
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
');

$db->exec('
    CREATE TABLE shipping_methods (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL DEFAULT 0,
        delivery_days TEXT NOT NULL DEFAULT ""
    )
');

$db->exec('
    CREATE TABLE payment_methods (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL DEFAULT 0
    )
');

$db->exec('
    CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_id INTEGER NOT NULL,
        shipping_method_id INTEGER NOT NULL,
        payment_method_id INTEGER NOT NULL,
        shipping_price REAL NOT NULL DEFAULT 0,
        payment_price REAL NOT NULL DEFAULT 0,
        note TEXT NOT NULL DEFAULT "",
        total_price REAL NOT NULL,
        status TEXT NOT NULL DEFAULT "new",
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id),
        FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
    )
');

$db->exec('
    CREATE TABLE order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        product_name TEXT NOT NULL,
        variant TEXT NOT NULL DEFAULT "",
        quantity INTEGER NOT NULL,
        unit_price REAL NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    )
');

echo "Tabulky vytvořeny.\n";

// ============================================================
// Vzorová data – téma: Sportovní e-shop
// ============================================================

// Kategorie
$categories = [
    ['Disky na dohazování', 'puttery', 'assets/images/putter.png', 'Puttery pro přesné dohazování do koše.'],
    ['Disky na střední vzdálenost', 'midrange', 'assets/images/midrange.png', 'Midrange disky pro střední vzdálenosti.'],
    ['Disky na dlouhou vzdálenost', 'fairway', 'assets/images/driver.png', 'Fairway drivers pro dlouhé a přesné hody.'],
    ['Disky na velmi dlouhou vzdálenost', 'distance', 'assets/images/ddriver.png', 'Distance drivers pro maximální vzdálenosti.'],
    ['Batohy', 'batohy', 'assets/images/prislusenstvi.png', 'Batohy a tašky pro discgolf.'],
    ['Příslušenství', 'prislusenstvi', 'assets/images/obleceni.png', 'Oblečení a příslušenství pro discgolf.'],
];

$catStmt = $db->prepare('INSERT INTO categories (name, slug, image, description) VALUES (?, ?, ?, ?)');
foreach ($categories as $cat) {
	$catStmt->execute($cat);
}

echo "Kategorie vloženy.\n";

// Produkty
$products = [
    // Puttery (category_id = 1)
    [1, 'D-Line Flex 2 P1', 'd-line-flex-2-p1', 339, NULL, 'Všemi oblíbený putter od Discmanie ve střední tvrdosti.', 'assets/images/p1.jpg', 0],
    [1, 'Zero Medium Pure', 'zero-medium-pure', 349, NULL, 'Klasický, rychlý putter od Latitude 64 ve střední tvrdosti.', 'assets/images/pure.jpg', 0],
    [1, 'PM Luna', 'pm-luna', 599, NULL, 'Laserově přesný putter od Discraftu.', 'assets/images/luna.jpg', 0],
    [1, 'Eclipse Proton 2.0 Proxy', 'eclipse-proton-proxy', 549, NULL, 'Rovný putter s vynikající svítivostí od Axiom Discs.', 'assets/images/proxy.jpg', 0],
    [1, 'DX Aviar Putter', 'dx-aviar-putter', 289, NULL, 'Legendární putter od Innova Discs už přes 30 let.', 'assets/images/aviar.jpg', 0],
    [1, 'K1 Berg', 'k1-berg', 499, NULL, 'Pomalý letec od Kastaplastu který nikam neuletí.', 'assets/images/berg.jpg', 0],

    // Midrange (category_id = 2)
    [2, 'C-Line MD3', 'c-line-md3', 549, NULL, 'Midrange od Discmanie používaný profesionály i amatéry pro svou všestrannost.', 'assets/images/md3.jpg', 0],
    [2, 'Star Jay', 'star-jay', 399, 489, 'Midrange od Innovy na který se můžete spolehnout.', 'assets/images/jay.jpg', 1],
    [2, 'Lucid Justice', 'lucid-justice', 479, NULL, 'Tento disk od Dynamic Discs nepřekvapí žádný vítr.', 'assets/images/justice.jpg', 0],
    [2, 'Z Zone', 'z-zone', 469, NULL, 'Nejprodávanější midrange od Discraftu vhodný pro všechny úrovně hráčů.', 'assets/images/zone.jpg', 0],
    [2, 'Neo Origin', 'neo-origin', 459, NULL, 'Vhodný disk pro začátečníky od Discmanie.', 'assets/images/origin.jpg', 0],
    [2, 'Neutron Hex', 'neutron-hex', 499, NULL, 'Rovný midrange disk od Axiom Discs vhodný pro kontrolované hody.', 'assets/images/hex.jpg', 0],

    // Fairway drivers (category_id = 3)
    [3, 'S Line FD', 's-line-fd', 559, NULL, 'Fairway driver od Discmanie s vynikající přesností a kontrolou letu.', 'assets/images/fd.jpg', 0],
    [3, 'S Line FD3', 's-line-fd3', 559, NULL, 'Stabilní driver pro dlouhé a přesné hody.', 'assets/images/fd3.jpg', 0],
    [3, 'Star Eagle', 'star-eagle', 479, NULL, 'Všestranný driver od Innovy vhodný pro různé herní styly.', 'assets/images/eagle.jpg', 0],
    [3, 'Proton Tesla', 'proton-tesla', 529, NULL, 'Spolehlivý driver od MVP Discs pro dlouhé hody.', 'assets/images/tesla.jpg', 0],
    [3, 'Z Anax', 'z-anax', 549, NULL, 'Rovný let se stabilním fadem na konci.', 'assets/images/anax.jpg', 0],
    [3, 'Gold Saint', 'gold-saint', 479, NULL, 'Skvělý pro začátečníky i pokročilé hráče.', 'assets/images/saint.jpg', 0],

    // Distance drivers (category_id = 4)
    [4, 'Star Destroyer', 'star-destroyer', 379, 489, 'Nejznámější a nejprodávanější distance driver na světě od Innova Discs.', 'assets/images/destroyer.jpg', 1],
    [4, 'S Line DD3', 's-line-dd3', 559, NULL, 'Distance driver od Discmanie pro pokročilé hráče.', 'assets/images/dd3.jpg', 0],
    [4, 'Royal Grand Grace', 'royal-grand-grace', 619, NULL, 'Skvělý driver pro dlouhé a přesné hody.', 'assets/images/grace.jpg', 0],
    [4, 'C-Line DD', 'c-line-dd', 559, NULL, 'Distance driver od Discmanie vhodný pro začátečníky.', 'assets/images/dd.jpg', 0],
    [4, 'Z Hades', 'z-hades', 549, NULL, 'Distance driver od Discraftu pro maximální vzdálenosti.', 'assets/images/hades.jpg', 0],
    [4, 'S Line PD2', 's-line-pd2', 559, NULL, 'Velice stabilní distance driver od Discmanie.', 'assets/images/pd2.jpg', 0],

    // Batohy (category_id = 5)
    [5, 'Latitude 64 Luxury Bag E5', 'latitude-64-e5', 4899, NULL, 'Nejprémiovější batoh od Latitude 64.', 'assets/images/E5.jpg', 0],
    [5, 'Discmania Fanatic Go Bag', 'discmania-go-bag', 749, NULL, 'Skvělý batoh pro začátečníky.', 'assets/images/go.jpg', 0],
    [5, 'Grip Bag AX5', 'grip-bag-ax5', 5499, 6699, 'Nejznámější batoh od Grip Equipment.', 'assets/images/grip.jpg', 1],
    [5, 'MVP Shuttle Bag', 'mvp-shuttle-bag', 1299, NULL, 'Skvělý batoh pro všechny.', 'assets/images/MVP.jpg', 0],
    [5, 'Dynamic Discs Trooper Backpack', 'dd-trooper-backpack', 1399, NULL, 'Skvělý batoh za skvělou cenu.', 'assets/images/trooper.jpg', 0],

    // Příslušenství (category_id = 6)
    [6, 'Marker Discmania', 'marker-discmania', 59, NULL, 'Značkovací kolečko od Discmanie pro označení polohy disku.', 'assets/images/marker.jpg', 0],
    [6, 'Ručník Discmania', 'rucnik-discmania', 249, NULL, 'Ručník z mikrovlákna s logem Discmania.', 'assets/images/rucnik.jpg', 0],
    [6, 'Tričko Anthony Barela', 'tricko-anthony-barela', 1549, NULL, 'S tímto trikem budete házet daleko jako Anthony.', 'assets/images/AB.jpg', 0],
    [6, 'Pláštěnka Grip EQ-X', 'plastenka-grip-eq', 729, NULL, 'Pláštěnka na Grip batohy.', 'assets/images/plastenka.jpg', 0],
    [6, 'Kšiltovka Innova', 'ksiltovka-innova', 659, NULL, 'Kšiltovka se kterou budete házet ještě lépe.', 'assets/images/ksiltovka.jpg', 0],
    [6, 'Deštník Innova', 'destnik-innova', 1199, NULL, 'Velký deštník, který vás udrží v suchu.', 'assets/images/destnik.jpg', 0],
];

$prodStmt = $db->prepare('
    INSERT INTO products (category_id, name, slug, price, original_price, description, image, featured)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
');

foreach ($products as $prod) {
	$prodStmt->execute($prod);
}

echo "Produkty vloženy (" . count($products) . ").\n";

// Obrázky produktů (galerie) – ukázkově pro pár produktů
$images = [
	// Nike Pegasus (product_id = 1)
	[1, 'assets/images/produkty/pegasus-41-2.svg', 1],
	[1, 'assets/images/produkty/pegasus-41-3.svg', 2],
	[1, 'assets/images/produkty/pegasus-41-4.svg', 3],

	// Specialized Allez (product_id = 6)
	[6, 'assets/images/produkty/allez-sport-2.svg', 1],
	[6, 'assets/images/produkty/allez-sport-3.svg', 2],

	// Osprey Atmos (product_id = 16)
	[16, 'assets/images/produkty/osprey-atmos-2.svg', 1],
	[16, 'assets/images/produkty/osprey-atmos-3.svg', 2],

	// Atomic Redster (product_id = 26)
	[26, 'assets/images/produkty/atomic-redster-2.svg', 1],
	[26, 'assets/images/produkty/atomic-redster-3.svg', 2],

	// Nike Mercurial (product_id = 24)
	[24, 'assets/images/produkty/mercurial-superfly-2.svg', 1],
	[24, 'assets/images/produkty/mercurial-superfly-3.svg', 2],
];

$imgStmt = $db->prepare('INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)');
foreach ($images as $img) {
	$imgStmt->execute($img);
}

echo "Obrázky vloženy.\n";

// Parametry produktů – type: 'select' = volitelný (dropdown), 'info' = pouze informační
$parameters = [
    // Puttery (product_id 1-6) - barva a váha
    [1, 'Barva', 'Červená, Modrá, Oranžová, Žlutá', 'select'],
    [1, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [2, 'Barva', 'Červená, Modrá, Fialová, Žlutá', 'select'],
    [2, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [3, 'Barva', 'Červená, Modrá, Fialová, Žlutá', 'select'],
    [3, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [4, 'Barva okraje', 'Červená, Modrá, Fialová, Žlutá', 'select'],
    [4, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [5, 'Barva', 'Červená, Modrá, Fialová, Žlutá', 'select'],
    [5, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [6, 'Barva', 'Červená, Modrá, Fialová, Žlutá', 'select'],
    [6, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],

    // Midrange (product_id 7-12)
    [7, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [7, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [8, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [8, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [9, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [9, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [10, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [10, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [11, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [11, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [12, 'Barva', 'Červená/Zelená, Modrá/Bílá, Zelená/Bílá, Žlutá/Oranžová', 'select'],
    [12, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],

    // Fairway drivers (product_id 13-18)
    [13, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [13, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [14, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [14, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [15, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [15, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [16, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [16, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [17, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [17, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [18, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [18, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],

    // Distance drivers (product_id 19-24)
    [19, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [19, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [20, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [20, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [21, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [21, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [22, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [22, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [23, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [23, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],
    [24, 'Barva', 'Červená, Modrá, Zelená, Žlutá', 'select'],
    [24, 'Váha', '173-175g, 170-172g, 165-169g, 160-164g', 'select'],

    // Batohy (product_id 25-29) - jen barva
    [25, 'Barva', 'Černá, Béžová, Červená', 'select'],
    [26, 'Barva', 'Černá, Béžová, Červená', 'select'],
    [27, 'Barva', 'Černá, Béžová', 'select'],
    [28, 'Barva', 'Černá, Béžová, Červená', 'select'],
    [29, 'Barva', 'Černá, Béžová, Červená', 'select'],

// Příslušenství (product_id 30-35)
    [30, 'Barva', 'Černá, Zelená, Červená, Modrá', 'select'],
    [31, 'Barva', 'Černá, Zelená, Červená, Modrá', 'select'],
    [33, 'Velikost', 'M, L, XL', 'select'],
    [34, 'Barva', 'Černá, Béžová, Červená', 'select'],
    [35, 'Barva', 'Černá, Béžová, Červená', 'select'],
];

$paramStmt = $db->prepare('INSERT INTO product_parameters (product_id, name, value, type) VALUES (?, ?, ?, ?)');
foreach ($parameters as $param) {
	$paramStmt->execute($param);
}

echo "Parametry vloženy.\n";

// Způsoby dopravy
$shippingMethods = [
	['Osobní odběr', 0, 'Ihned k vyzvednutí'],
	['Zásilkovna', 69, '2–3 pracovní dny'],
	['PPL', 99, '1–2 pracovní dny'],
	['Česká pošta', 129, '3–5 pracovních dnů'],
	['DPD', 89, '1–2 pracovní dny'],
];

$shipStmt = $db->prepare('INSERT INTO shipping_methods (name, price, delivery_days) VALUES (?, ?, ?)');
foreach ($shippingMethods as $method) {
	$shipStmt->execute($method);
}

echo "Způsoby dopravy vloženy.\n";

// Způsoby platby
$paymentMethods = [
	['Kartou online', 0],
	['Bankovním převodem', 0],
	['Dobírkou', 39],
	['Apple Pay / Google Pay', 0],
];

$payStmt = $db->prepare('INSERT INTO payment_methods (name, price) VALUES (?, ?)');
foreach ($paymentMethods as $method) {
	$payStmt->execute($method);
}

echo "Způsoby platby vloženy.\n";

// Vzorový zákazník
$db->exec('
    INSERT INTO customers (first_name, last_name, email, phone, street, city, zip)
    VALUES ("Jan", "Novák", "jan.novak@email.cz", "+420 777 123 456", "Sportovní 42", "Praha", "11000")
');

echo "Vzorový zákazník vytvořen.\n";

// Vzorová objednávka (Zásilkovna = id 2, cena 69 Kč; Kartou online = id 1, cena 0 Kč)
// Celková cena: 3299 + 599 + 490 + 69 (doprava) + 0 (platba) = 4457
$db->exec('
    INSERT INTO orders (customer_id, shipping_method_id, payment_method_id, shipping_price, payment_price, note, total_price, status)
    VALUES (1, 2, 1, 69, 0, "Prosím zabalit jako dárek.", 4457, "new")
');

$db->exec('
    INSERT INTO order_items (order_id, product_id, product_name, variant, quantity, unit_price)
    VALUES
        (1, 1, "Nike Air Zoom Pegasus 41", "Barva: Černá, Velikost: 42", 1, 3299),
        (1, 12, "Expandér sada 5 ks", "", 1, 599),
        (1, 15, "Foam Roller 45 cm", "", 1, 490)
');

// Indexy pro rychlejší vyhledávání
$db->exec('CREATE INDEX idx_products_category ON products(category_id)');
$db->exec('CREATE INDEX idx_products_slug ON products(slug)');
$db->exec('CREATE INDEX idx_products_featured ON products(featured)');
$db->exec('CREATE INDEX idx_categories_slug ON categories(slug)');
$db->exec('CREATE INDEX idx_order_items_order ON order_items(order_id)');
$db->exec('CREATE INDEX idx_product_images_product ON product_images(product_id)');
$db->exec('CREATE INDEX idx_product_params_product ON product_parameters(product_id)');

echo "\nDatabáze úspěšně inicializována!\n";
echo "Soubor: $dbPath\n";
