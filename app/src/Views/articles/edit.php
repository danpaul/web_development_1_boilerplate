<?php
/** @var \App\ViewModels\ArticleViewModel $model */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main>
    <p><a href="/articles">Back to articles</a></p>
    <?php $article = $model->article; ?>
    <?php require __DIR__ . '/../partials/article-form.php'; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
