# Plan: Completed todos tracking

Spec: [./spec.md](./spec.md)

Add a `POST /todo/complete` route and `TodoController::complete` that move an item from `app/data/todos.json` to a new `app/data/completed.json`. Generalize the controller's storage helpers to take a file path, show a read-only "Completed" section in `todo.php`, and update `AGENTS.md` and `README.md`.

## Technical context

- `app/public/index.php` has `GET /todo` → `index`, `POST /todo/add` → `add`, `POST /todo/delete` → `delete` on `App\Controllers\TodoController`.
- `TodoController` (MVC warning comment at the top) has constants `DATA_DIR` (`__DIR__ . '/../../data'`) and `DATA_FILE` (`DATA_DIR . '/todos.json'`), plus private helpers that are hard-wired to `DATA_FILE`:
  - `ensureStorage()`: `mkdir` + `chmod 0777` for the dir; creates the file as `[]` + `chmod 0666`
  - `readTodos()`: invalid JSON or a non-array → `[]`
  - `writeTodos()`
  - `redirect()`: 303 to `/todo`, then `exit`
- Items are `{"id": "<16 hex>", "text": "<string>"}`, newest first. `add` uses `array_unshift`. `delete` reads `$_POST['id']` (non-string → `''`) and writes only if something changed.
- `app/src/Views/todo.php` is one full page. `$todos` is in scope. Each `<li>` holds a `<span>` of escaped text and one Delete form. CSS: `li { display: flex; justify-content: space-between; … }`, `li form { margin: 0; }`.
- `/app/data/` is already in `.gitignore`, which covers `completed.json`.
- `AGENTS.md` line: `` `app/data/`: runtime JSON storage for the todo example (`todos.json`); created by `TodoController`, gitignored ``.
- `README.md` `### Todo example`: "to add and delete items" and "Items are stored in `app/data/todos.json` …".

## Target structure

- Change `app/public/index.php` (one route)
- Change `app/src/Controllers/TodoController.php`
- Change `app/src/Views/todo.php`
- Change `AGENTS.md` (`app/data/` line)
- Change `README.md` (`### Todo example`)
- Generated at runtime, not committed: `app/data/completed.json`

## Route (`app/public/index.php`)

Add after the delete route:

```php
$r->addRoute('POST', '/todo/complete', ['App\Controllers\TodoController', 'complete']);
```

## TodoController (`app/src/Controllers/TodoController.php`)

Keep the MVC warning comment.

Constants:

```php
private const DATA_DIR = __DIR__ . '/../../data';
private const TODOS_FILE = self::DATA_DIR . '/todos.json';        // renamed from DATA_FILE
private const COMPLETED_FILE = self::DATA_DIR . '/completed.json';
```

Helpers. These replace `readTodos` / `writeTodos`, and the logic stays the same:

- `ensureStorage(): void`: creates the dir as before, then for each of `[self::TODOS_FILE, self::COMPLETED_FILE]`, if it is missing, writes `'[]'` and runs `chmod 0666`.
- `readList(string $file): array`: calls `ensureStorage()`, then `json_decode(file_get_contents($file), true)`; a non-array result → `[]`.
- `writeList(string $file, array $items): void`: same `json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)`. No locking.
- `redirect()`: unchanged.

Public methods:

- `index($vars = [])`: `$todos = $this->readList(self::TODOS_FILE); $completed = $this->readList(self::COMPLETED_FILE);` then `require` the view.
- `add` / `delete`: behavior unchanged. Only replace the `readTodos()` / `writeTodos()` calls with `readList(self::TODOS_FILE)` / `writeList(self::TODOS_FILE, …)`.
- `complete($vars = [])`, new:

```php
$id = $_POST['id'] ?? '';
if (!is_string($id)) {
    $id = '';
}

$todos = $this->readList(self::TODOS_FILE);
foreach ($todos as $index => $todo) {
    if (($todo['id'] ?? null) === $id) {
        $completed = $this->readList(self::COMPLETED_FILE);
        array_unshift($completed, $todo);   // same {id, text} object, newest first
        unset($todos[$index]);
        $this->writeList(self::COMPLETED_FILE, $completed);
        $this->writeList(self::TODOS_FILE, $todos);
        break;
    }
}

$this->redirect();
```

- A nonexistent id writes nothing and redirects.
- No database access.

## View (`app/src/Views/todo.php`)

- Active list `<li>`: keep the `<span>` text. Wrap the two forms in a `<div>` so the flex row stays text-left and buttons-right:
  - `<form method="post" action="/todo/complete">` with hidden `id` (escaped) and `<button type="submit">Complete</button>`
  - the existing Delete form, unchanged, after Complete
- CSS: add `li div { display: flex; gap: 0.5rem; }`. Nothing else.
- After the active list block, add:

```php
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
```

- Completed items have no forms or buttons. No JavaScript.

## AGENTS.md

Change the `app/data/` line to:

`` - `app/data/`: runtime JSON storage for the todo example (`todos.json`, `completed.json`); created by `TodoController`, gitignored ``

## README.md

In `### Todo example`:

- "to add and delete items" → "to add, complete and delete items. Completed items move to a read-only Completed list on the same page."
- "Items are stored in `app/data/todos.json`" → "Items are stored in `app/data/todos.json` and completed items in `app/data/completed.json`". Keep the rest of the sentence.

## Implementation order

1. Refactor the `TodoController` storage: constants, `ensureStorage`, `readList` / `writeList`, and update `index`, `add`, `delete`.
2. Add `TodoController::complete`.
3. Add the `POST /todo/complete` route.
4. Update `todo.php`: the Complete button and the Completed section.
5. Update `AGENTS.md` and `README.md`.
6. Verify (see `task.md`).

## Out of scope

- Any other todo functionality: deleting or un-completing completed items, editing, reordering, timestamps
- Database tables, migrations or DB access
- JavaScript, CSRF tokens, flash messages, file locking
- A layout, partials or view helpers
- Changes to `.gitignore`, `HomeController`, `HelloController`, `nginx.conf`, `PHP.Dockerfile`, `docker-compose.yml`
- `composer dump-autoload`, since no new classes are added
