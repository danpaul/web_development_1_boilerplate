<?php

/** @var \App\Models\ProductModel[] $products */

?>

<div class="container mt-4">
    <h2 class="mb-4">Products</h2>
    <div class="row">
        <?php foreach ($products as $product) { ?>
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <?php require(__DIR__ . "/product_card.php"); ?>
            </div>
        <?php } ?>
    </div>
</div>