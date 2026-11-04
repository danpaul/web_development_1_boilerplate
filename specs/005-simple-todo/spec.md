## Goal

Create a simple todo application at `/todo` where items can be added and removed, stored in a JSON file instead of the database.

## Requirements

- Only add and remove functionality; completed items are not tracked
- Routes in `app/public/index.php`, all mapped to `App\Controllers\TodoController`:
  - `GET /todo`: shows the add form and the list
  - `POST /todo/add`: adds an item, then redirects to `GET /todo`
  - `POST /todo/delete`: removes an item by id, then redirects to `GET /todo`
- No JavaScript; the page updates through full-page Post/Redirect/Get
- Storage is a JSON file at `app/data/todos.json`, outside the web root; do not use the database
- The controller creates `app/data/` and `todos.json` if they are missing; `app/data/` is added to `.gitignore`
- Each item is stored as an object with a generated unique `id` and its `text`; the delete form sends the item's `id`
- New items are added to the top of the list
- The add input has the HTML `required` attribute; the server also trims the text and silently ignores empty or whitespace-only input, then redirects to `/todo`
- No maximum length on item text
- Item text is HTML-escaped when displayed
- Deleting an id that does not exist changes nothing and redirects to `/todo`
- When the list is empty, the page shows a short message (e.g. "No todos yet.")
- All PHP functionality (reading/writing the file, handling add and delete, redirects) lives in `app/src/Controllers/TodoController.php`
- `TodoController.php` contains a strong warning comment that this organization violates MVC and is not suitable for a student's final project
- The view is a single full PHP page at `app/src/Views/todo.php` containing the add form and the list, with a delete button per item; no further view organization
- Only bare-bones functional styles

## Acceptance Criteria

- Visiting `/todo` shows an add form and the current list of items
- Submitting the add form adds the item to the top of the list and the page shows the updated list
- Clicking an item's delete button removes that item and the page shows the updated list
- Submitting empty or whitespace-only text adds nothing
- When there are no items, the page shows the empty-list message
- Items persist across page reloads via `app/data/todos.json`
- `app/data/` is not tracked by git
- `TodoController.php` contains the MVC warning

## Out of Scope

- Data migrations
- Handling completed items beyond simply deleting them
