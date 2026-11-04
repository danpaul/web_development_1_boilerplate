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
        li div { display: flex; gap: 0.5rem; }
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
                    <div>
                        <form method="post" action="/todo/complete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($todo['id']) ?>">
                            <button type="submit">Complete</button>
                        </form>
                        <form method="post" action="/todo/delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($todo['id']) ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Completed</h2>
    <?php if (empty($completed)): ?>
        <p>No completed todos yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($completed as $item): ?>
                <li><span><?= htmlspecialchars($item['text']) ?></span></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
