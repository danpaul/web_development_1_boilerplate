<?php
/** @var \App\ViewModels\ArticlesViewModel $model */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main>
    <?php if (empty($model->articles)): ?>
        <p>No articles found.</p>
    <?php else: ?>
        <?php foreach ($model->articles as $article): ?>
            <?php require __DIR__ . '/../partials/article.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
