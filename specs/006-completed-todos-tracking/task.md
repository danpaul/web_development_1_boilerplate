# Tasks: Completed todos tracking

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## TodoController storage

- [x] Rename `DATA_FILE` to `TODOS_FILE`; add `COMPLETED_FILE` (`DATA_DIR . '/completed.json'`)
- [x] `ensureStorage()` creates both `todos.json` and `completed.json` as `[]` + `chmod 0666` when missing (dir handling unchanged)
- [x] Replace `readTodos()` / `writeTodos()` with `readList(string $file)` / `writeList(string $file, array $items)`, keeping the same decode and encode logic (non-array → `[]`, same `json_encode` flags, no locking)
- [x] `index()` passes both `$todos` and `$completed` to the view
- [x] `add()` and `delete()` use `readList` / `writeList(self::TODOS_FILE, …)`; their behavior is otherwise unchanged
- [x] Keep the MVC warning comment

## Complete action

- [x] Add `complete($vars = [])`: read `$_POST['id']` (non-string → `''`); if it is in `todos.json`, `array_unshift` the same `{id, text}` object onto `completed.json`, remove it from `todos.json`, and write both
- [x] A nonexistent id writes nothing; always `redirect()`
- [x] No database access

## Route

- [x] In `app/public/index.php`, add `POST /todo/complete` → `['App\Controllers\TodoController', 'complete']` after the delete route
- [x] Do not change the existing routes

## View

- [x] In each active `<li>`, wrap the forms in a `<div>`: first a `POST /todo/complete` form with the escaped hidden `id` and a "Complete" button, then the existing Delete form
- [x] Add CSS `li div { display: flex; gap: 0.5rem; }` and no other styles
- [x] Below the active list, add `<h2>Completed</h2>`: empty → `<p>No completed todos yet.</p>`, otherwise a `<ul>` of `<li><span>` with text escaped via `htmlspecialchars`
- [x] Completed items have no forms or buttons; no JavaScript

## Docs

- [x] `AGENTS.md`: change the `app/data/` line to list `todos.json`, `completed.json`
- [x] `README.md` `### Todo example`: mention Complete and the read-only Completed list, and that completed items are stored in `app/data/completed.json`

## Verify

- [x] With no `completed.json`, visiting `http://localhost/todo` creates it as `[]` and shows "No completed todos yet."
- [x] Adding and deleting active todos still works as before
- [x] Clicking Complete on an item removes it from the active list and shows it at the top of Completed; the URL is `/todo` afterwards
- [x] Completing "a" then "b" shows "b" above "a" in Completed
- [x] `completed.json` items have `id` and `text` only, with the same `id` the item had in `todos.json`
- [x] Posting a nonexistent id (`curl -X POST -d "id=nope" http://localhost/todo/complete`) leaves both JSON files unchanged
- [x] Completed `<b>x</b>` displays as literal text
- [x] Completed items have no buttons
- [x] Completed items persist after reload and after `docker compose down` / `up`
- [x] `git check-ignore app/data/completed.json` matches
- [x] `GET /` and `GET /hello/Alice` still work
- [x] Only the files in the plan's Target structure changed
