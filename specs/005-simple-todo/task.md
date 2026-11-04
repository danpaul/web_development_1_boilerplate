# Tasks: Simple todo

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Git ignore

- [x] Add `/app/data/` to `.gitignore` under a `# App data` comment

## TodoController

- [x] Add `app/src/Controllers/TodoController.php` (namespace `App\Controllers`)
- [x] Add a prominent `WARNING` block comment at the top: this organization violates MVC (storage, request handling and redirects in one controller) and is not suitable for a student's final project
- [x] Constants `DATA_DIR` (`__DIR__ . '/../../data'`) and `DATA_FILE` (`DATA_DIR . '/todos.json'`)
- [x] `ensureStorage()`: create `app/data/` with `mkdir(..., 0777, true)` + `chmod 0777` if missing; create `todos.json` as `[]` + `chmod 0666` if missing
- [x] `readTodos()`: calls `ensureStorage()`, decodes JSON as array; invalid JSON or non-array → `[]`
- [x] `writeTodos()`: `json_encode(array_values(...), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)`; no file locking
- [x] `redirect()`: `header('Location: /todo', true, 303); exit;`
- [x] `index($vars = [])`: reads todos and `require`s `app/src/Views/todo.php` with `$todos` in scope
- [x] `add($vars = [])`: trims `$_POST['text']` (non-string → `''`); if non-empty, prepend `['id' => bin2hex(random_bytes(8)), 'text' => $text]` and write; always redirect
- [x] `delete($vars = [])`: removes the item whose `id === $_POST['id']`; write only if something was removed; always redirect
- [x] No max length check on text; no database access

## View

- [x] Add `app/src/Views/todo.php` as one full HTML page (doctype, charset, viewport, `<title>Todo</title>`)
- [x] Bare-bones inline `<style>` only; no JavaScript, no layout or partials
- [x] Add form: `POST /todo/add`, `<input type="text" name="text" required>`, Add button
- [x] Empty list → `<p>No todos yet.</p>`
- [x] Otherwise `<ul>` with one `<li>` per item: text via `htmlspecialchars`, and a `POST /todo/delete` form with hidden `id` (escaped) and a Delete button

## Routes

- [x] In `app/public/index.php` add `GET /todo` → `index`, `POST /todo/add` → `add`, `POST /todo/delete` → `delete`, all on `App\Controllers\TodoController`
- [x] Do not change the existing routes
- [x] Run `docker compose run --rm php composer dump-autoload`

## Docs

- [x] `AGENTS.md` Project structure: add `TodoController` to the controllers line; add `app/src/Views/` and `app/data/` (runtime JSON storage, created by `TodoController`, gitignored)
- [x] `README.md`: add `### Todo example` after `### NGINX` (link to `http://localhost/todo`, storage in `app/data/todos.json`, code locations, not a model for a final project)

## Verify

- [x] With no `app/data/`, visiting `http://localhost/todo` shows the add form and "No todos yet.", and creates `app/data/todos.json` containing `[]`
- [x] Adding "first" then "second" shows "second" above "first"; the URL is `/todo` after each submit
- [x] Submitting only spaces (e.g. `curl -X POST -d "text=%20%20" http://localhost/todo/add`) adds nothing and redirects to `/todo`
- [x] Adding `<b>x</b>` displays the literal text, not bold
- [x] Clicking Delete removes only that item; posting a nonexistent id leaves `todos.json` unchanged
- [x] Items persist after reload and after `docker compose down` / `up`
- [x] `todos.json` items have `id` and `text` only
- [x] `git status` does not show `app/data/`; `git check-ignore app/data/todos.json` matches
- [x] `TodoController.php` contains the MVC warning
- [x] `GET /` and `GET /hello/Alice` still work
- [x] Only the files in the plan's Target structure changed
