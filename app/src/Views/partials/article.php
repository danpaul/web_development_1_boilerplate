<?php
/** @var \App\Models\Article $article */
?>
<article>
    <h2><?= htmlspecialchars($article->title) ?></h2>
    <p>By <?= htmlspecialchars($article->author) ?> on <time><?= htmlspecialchars($article->created_at ?? '') ?></time></p>
    <p><?= htmlspecialchars($article->content) ?></p>
</article>
