<?php

/** @var \App\ViewModels\ProductsViewModel $vm */

require __DIR__ . '/../partials/header.php';

?>

<?php

require __DIR__ . '/../partials/hero.php';

$products = $vm->products;
require __DIR__ . '/../partials/product_grid.php';

require __DIR__ . '/../partials/footer.php';
