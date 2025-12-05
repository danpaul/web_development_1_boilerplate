<?php

/** @var $rating int; */
/** @var $ratingCount int; */  ?>

<div class="d-flex align-items-center mb-3">
    <div class="text-warning me-2">
        <?php
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        for ($i = 0; $i < $fullStars; $i++) {
            echo '★';
        }
        if ($hasHalfStar) {
            echo '½';
        }
        for ($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++) {
            echo '☆';
        }
        ?>
    </div>
    <small class="text-muted">
        <?= number_format($product->ratingRate, 1); ?> (<?= $ratingCount; ?> reviews)
    </small>
</div>