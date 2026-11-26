<?php
/** @var \App\Models\Article $article */
$isNew = $article->id === null;
$action = $isNew ? '/articles/create' : '/articles/' . $article->id . '/edit';
?>
<form method="post" action="<?= htmlspecialchars($action) ?>">
    <p>
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($article->title ?? '') ?>" required>
    </p>
    <p>
        <label for="content">Content</label>
        <textarea id="content" name="content" required><?= htmlspecialchars($article->content ?? '') ?></textarea>
    </p>
    <p>
        <label for="author">Author</label>
        <input type="text" id="author" name="author" value="<?= htmlspecialchars($article->author ?? '') ?>" required>
    </p>
    <p>
        <?php if ($isNew): ?>
        <button type="submit">Create</button>
        <?php else: ?>
        <button type="submit">Save</button>
        <a href="<?= htmlspecialchars('/articles/' . $article->id) ?>">Cancel</a>
        <?php endif; ?>
    </p>
</form>