<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todo</title>
    <style>
        body { font-family: sans-serif; max-width: 40rem; margin: 2rem auto; padding: 0 1rem; }
        ul { list-style: none; padding: 0; }
        li { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 0.5rem 0; border-bottom: 1px solid #ccc; }
        li form { margin: 0; }
    </style>
</head>
<body>
    <h1>Todo</h1>

    <form method="post" action="/todo/add">
        <input type="text" name="text" required autofocus>
        <button type="submit">Add</button>
    </form>

    <?php if (empty($todos)): ?>
        <p>No todos yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($todos as $todo): ?>
                <li>
                    <span><?= htmlspecialchars($todo['text']) ?></span>
                    <form method="post" action="/todo/delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($todo['id']) ?>">
                        <button type="submit">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
