## Goal

Let users mark a todo as complete on the `/todo` page. Completing a todo moves it from the active list to a completed list, which is stored in its own JSON file and shown on the same page.

## Requirements

- Extends the existing todo app (spec 005). Existing add and delete behavior stays unchanged
- New route in `app/public/index.php`: `POST /todo/complete`, mapped to `App\Controllers\TodoController::complete`. It moves an item by `id` from the active list to the completed list, then redirects to `GET /todo` (Post/Redirect/Get, no JavaScript)
- Completed items are stored in `app/data/completed.json`, next to `todos.json` (outside the web root and already gitignored via `app/data/`). Do not use the database
- The controller creates `completed.json` if it is missing, the same way it creates `todos.json`
- A completed item is stored as the same `{id, text}` object it had in `todos.json`, and is removed from `todos.json`
- Newly completed items are added to the top of the completed list (most recently completed first)
- Completing an id that does not exist in the active list changes nothing and redirects to `/todo`
- Each active item has a "Complete" button next to its existing "Delete" button; the complete form sends the item's `id`
- The view `app/src/Views/todo.php` shows a "Completed" section below the active list
- Completed items are read-only: no delete, "mark incomplete", or other buttons
- Completed item text is HTML-escaped when displayed
- When the completed list is empty, the section shows a short message (e.g. "No completed todos yet.")
- All PHP functionality stays in `app/src/Controllers/TodoController.php`, which keeps its MVC warning comment. The view stays a single page, and styles stay bare-bones

## Acceptance Criteria

- Adding and deleting active todos still works in the browser
- Clicking an item's "Complete" button removes it from the active list and shows it at the top of the "Completed" section
- Completed items persist across page reloads via `app/data/completed.json`
- Completed items have no action buttons
- When there are no completed items, the "Completed" section shows the empty message
- `app/data/completed.json` is not tracked by git

## Out of Scope

- any additional todo functionality
