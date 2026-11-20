<?php
/** @var \App\ViewModels\ArticleViewModel $model */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main>
    <?php $article = $model->article; ?>
    <?php require __DIR__ . '/../partials/article.php'; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
