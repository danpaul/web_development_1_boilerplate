<?php
/** @var \App\ViewModels\ArticleViewModel $model */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main>
    <?php $article = $model->article; ?>
    <?php require __DIR__ . '/../partials/article.php'; ?>
    <p><a href="<?= htmlspecialchars('/articles/' . $article->id . '/edit') ?>">Edit</a></p>
    <form method="post" action="<?= htmlspecialchars('/articles/' . $article->id . '/delete') ?>">
        <button type="submit">Delete</button>
    </form>
    <p><a href="/articles">Back to articles</a></p>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
