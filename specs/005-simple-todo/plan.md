# Plan: Simple todo

Spec: [./spec.md](./spec.md)

Add a `/todo` page backed by `app/data/todos.json`, with all logic in a single `TodoController`, one view file, three routes, a `.gitignore` entry, and short doc updates in `AGENTS.md` and `README.md`.

## Technical context

- `app/public/index.php`: FastRoute `simpleDispatcher`; routes are `[App\Controllers\X, 'method']`; the controller is instantiated with no args and called as `$controller->$method($vars)`. Existing routes: `GET /`, `GET /hello/{name}`.
- PSR-4 `App\` → `app/src/`. `app/src/` has only `Controllers/` (`HomeController`, `HelloController`, which echo directly). `app/src/Views/` does not exist.
- nginx root is `app/public`, so `app/data/` is outside the web root.
- php-fpm (`php:8.5-fpm`) runs as `www-data`; `./app` is bind-mounted at `/app`. On Linux hosts, files `www-data` creates need open permissions for the host user to manage, and vice versa.
- `.gitignore` has no `app/data` entry.
- `README.md` `## Usage` has `### Composer Autoload`, `### NGINX`, `### PHPMyAdmin`, `### Stopping the docker container`.
- `AGENTS.md` **Project structure** lists `app/src/Controllers/`: `HomeController`, `HelloController`.

## Target structure

- Add `app/src/Controllers/TodoController.php`
- Add `app/src/Views/todo.php`
- Change `app/public/index.php` (three routes)
- Change `.gitignore` (add `/app/data/`)
- Change `AGENTS.md` (Project structure)
- Change `README.md` (new `### Todo example` subsection)
- Generated at runtime, not committed: `app/data/todos.json`

## Routes (`app/public/index.php`)

Add after the existing routes:

```php
$r->addRoute('GET', '/todo', ['App\Controllers\TodoController', 'index']);
$r->addRoute('POST', '/todo/add', ['App\Controllers\TodoController', 'add']);
$r->addRoute('POST', '/todo/delete', ['App\Controllers\TodoController', 'delete']);
```

## TodoController (`app/src/Controllers/TodoController.php`)

- Namespace `App\Controllers`, class `TodoController`.
- Top of file (above the class): a prominent block comment, e.g. `WARNING:` banner, stating that putting file storage, request handling and redirects all in one controller violates MVC (no model/repository layer, controller does data access) and that this structure is **not suitable for a student's final project**; it exists only as a minimal example.
- Constants:
  - `private const DATA_DIR = __DIR__ . '/../../data';`
  - `private const DATA_FILE = self::DATA_DIR . '/todos.json';`
- Data shape: JSON array of `{"id": "<16 hex chars>", "text": "<string>"}`, newest first.

Public methods (all take `$vars = []`):

- `index($vars = [])`: `$todos = $this->readTodos();` then `require __DIR__ . '/../Views/todo.php';` (the view uses `$todos`).
- `add($vars = [])`:
  - `$text = $_POST['text'] ?? '';` non-string → treat as `''`; `$text = trim($text);`
  - If `$text !== ''`: read todos, `array_unshift($todos, ['id' => bin2hex(random_bytes(8)), 'text' => $text]);`, write.
  - Always `$this->redirect()`.
- `delete($vars = [])`:
  - `$id = $_POST['id'] ?? '';` non-string → `''`.
  - Read todos, filter out items whose `id === $id`, `array_values`; write only if the count changed.
  - Always `$this->redirect()`.

Private helpers:

- `ensureStorage()`: called at the start of `readTodos()`.
  - If `!is_dir(self::DATA_DIR)`: `mkdir(self::DATA_DIR, 0777, true); chmod(self::DATA_DIR, 0777);` (explicit `chmod` because `umask` reduces `mkdir`'s mode).
  - If `!file_exists(self::DATA_FILE)`: `file_put_contents(self::DATA_FILE, '[]'); chmod(self::DATA_FILE, 0666);`
- `readTodos(): array`: `ensureStorage()`; `json_decode(file_get_contents(self::DATA_FILE), true)`; if the result is not an array, return `[]` (invalid JSON is treated as an empty list).
- `writeTodos(array $todos): void`: `file_put_contents(self::DATA_FILE, json_encode(array_values($todos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));` No locking.
- `redirect(): void`: `header('Location: /todo', true, 303);` then `exit;`.

## View (`app/src/Views/todo.php`)

Single full HTML page; no layout/partials:

- `<!DOCTYPE html>`, `<html lang="en">`, `<meta charset="utf-8">`, viewport meta, `<title>Todo</title>`.
- Inline `<style>` with bare-bones rules only (e.g. `body` font/max-width/margin, list without bullets, item row with text and delete button inline). No external CSS, no JavaScript.
- `<h1>Todo</h1>`
- Add form: `<form method="post" action="/todo/add">` with `<input type="text" name="text" required autofocus>` and `<button type="submit">Add</button>`.
- If `empty($todos)`: `<p>No todos yet.</p>`.
- Else `<ul>`; per item `<li>`: `<?= htmlspecialchars($todo['text']) ?>` and `<form method="post" action="/todo/delete">` with `<input type="hidden" name="id" value="<?= htmlspecialchars($todo['id']) ?>">` and `<button type="submit">Delete</button>`.

## .gitignore

Add under a new `# App data` comment:

```
/app/data/
```

## AGENTS.md

In **Project structure**:

- Change the controllers line to: `app/src/Controllers/`: `HomeController`, `HelloController`, `TodoController`
- Add after it:
  - `app/src/Views/`: view templates (`todo.php`)
  - `app/data/`: runtime JSON storage for the todo example (`todos.json`); created by `TodoController`, gitignored

## README.md

Add `### Todo example` under `## Usage`, right after `### NGINX`:

- Go to [http://localhost/todo](http://localhost/todo) to add and delete items.
- Items are stored in `app/data/todos.json` (created automatically, gitignored), not in the database.
- Code: `app/src/Controllers/TodoController.php` and `app/src/Views/todo.php`.
- One sentence: it puts everything in one controller on purpose and is not a model for a final project's structure (see the warning in the controller).

## Implementation order

1. Add `/app/data/` to `.gitignore`.
2. Add `TodoController.php` (warning comment, helpers, `index`/`add`/`delete`).
3. Add `app/src/Views/todo.php`.
4. Add the three routes to `app/public/index.php`.
5. `docker compose run --rm php composer dump-autoload`.
6. Update `AGENTS.md` and `README.md`.
7. Verify (see `task.md`).

## Out of scope

- Data migrations, database tables, or any DB access
- Completed/done state, editing items, reordering
- JavaScript, CSRF tokens, flash/validation messages, max text length
- File locking or concurrency handling
- A layout, partials, or any view helper/organization beyond `todo.php`
- Changing `HomeController`, `HelloController`, `nginx.conf`, `PHP.Dockerfile`, `docker-compose.yml`
- Linking `/todo` from the home page
