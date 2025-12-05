<?php

/** @var \App\Models\ProductModel $product */

?>

<div class="card" style="width: 100%; height: 100%;">
    <a href="/<?= $product->id; ?>">
        <img src="<?= $product->image; ?>" class="card-img-top" alt="<?= $product->title; ?> image">
    </a>
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge bg-secondary"><?= htmlspecialchars($product->category); ?></span>
            <div class="text-end">
                <div class="fw-bold text-primary fs-5">$<?= number_format($product->price, 2); ?></div>
            </div>
        </div>
        <h5 class="card-title"><?= htmlspecialchars($product->title); ?></h5>
        <p class="card-text text-muted small"><?= htmlspecialchars($product->description); ?></p>
        <?php
        $rating = (int) round($product->ratingRate);
        $ratingCount = $product->ratingCount;
        require __DIR__ . '/rating.php';
        ?>
        <a href="/<?= $product->id; ?>" class="btn btn-primary w-100 mt-auto">Details</a>
    </div>
</div>