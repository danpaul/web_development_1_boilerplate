<?php
/** @var \App\Models\Article $article */
?>
<article>
    <h2><a href="<?= htmlspecialchars('/articles/' . $article->id) ?>"><?= htmlspecialchars($article->title) ?></a></h2>
    <p>By <?= htmlspecialchars($article->author) ?> on <time><?= htmlspecialchars($article->created_at ?? '') ?></time></p>
    <p><?= htmlspecialchars($article->content) ?></p>
</article>
