## Goal

Complete backend CRUD for articles: an HTML detail page for a single article, plus POST endpoints to create, update and delete articles. There are no forms, validation or other frontend for writes yet; the POST endpoints are exercised directly (e.g. with curl).

## Requirements

- Routes in `app/public/index.php` (FastRoute), all handled by `App\Controllers\ArticleController`. `{id}` is constrained to digits (`{id:\d+}`):
  - `GET /articles` → `index` (existing, unchanged)
  - `GET /articles/{id}` → `show`
  - `POST /articles/create` → `store`
  - `POST /articles/{id}/edit` → `update`
  - `POST /articles/{id}/delete` → `destroy`
- No `GET` form pages for create or edit, and no delete UI.
- POST endpoints read `title`, `content` and `author` from form-encoded `$_POST`. There is no validation; a missing field is stored as an empty string.
- `created_at` is never set or changed by the endpoints. Create relies on the column default; update changes only `title`, `content` and `author`.
- After a successful create, update or delete, the endpoint redirects (`Location: /articles`, HTTP 302) to the list (Post/Redirect/Get).
- If `{id}` does not match an existing article, `show`, `update` and `destroy` respond with HTTP 404 and `Not Found`, and change nothing.
- Implement the existing stub methods in `ArticleService` and `ArticleRepository` (the `IArticleService` and `IArticleRepository` signatures stay unchanged):
  - `get(int $id): ?Article` returns the article, or `null` if none exists
  - `create(Article $article): void` inserts `title`, `content`, `author`
  - `update(Article $article): void` updates `title`, `content`, `author` for `$article->id`
  - `delete(int $id): void` deletes the article
  - The service delegates to the repository. The repository uses PDO prepared statements with bound parameters and loads `Article` with `PDO::FETCH_CLASS`.
- Detail page:
  - Add `App\ViewModels\ArticleViewModel` in `app/src/ViewModels/ArticleViewModel.php`, data only: `public string $title` (the article's title) and `public \App\Models\Article $article`. Run `composer dump-autoload` after adding it.
  - Add the view `app/src/Views/articles/show.php`, typed with `/** @var \App\ViewModels\ArticleViewModel $model */`. It includes `partials/header.php`, `partials/article.php` (with `$article = $model->article`) and `partials/footer.php`.
  - Update the `@var` DocBlock in `partials/header.php` so it accepts either view model (`ArticlesViewModel|ArticleViewModel`). Both expose `$title`.
  - `show` assigns `$model` and `require`s the view, following the existing controller pattern.
- Follow the `AGENTS.md` conventions: escape all output with `htmlspecialchars`; no CSS; no render helper or base controller.
- Add `ArticleViewModel` to the `app/src/ViewModels/` line in the `AGENTS.md` project structure.

## Acceptance Criteria

- With the seeded database, `GET /articles/1` returns an HTML page whose `<title>` and heading are article 1's title, and which shows that article's title, author, `created_at` and content.
- `GET /articles/9999` (no such article) returns 404 `Not Found`.
- `POST /articles/create` with `title`, `content` and `author` inserts a new row with a `created_at` set by the database, and responds with a 302 redirect to `/articles`. The new article then appears on `/articles`.
- `POST /articles/{id}/edit` for an existing id updates that row's `title`, `content` and `author`, leaves `created_at` unchanged, and redirects to `/articles`.
- `POST /articles/{id}/delete` for an existing id removes the row and redirects to `/articles`.
- `POST /articles/9999/edit` and `POST /articles/9999/delete` return 404 and change no rows.
- `GET /articles/create`, `GET /articles/1/edit` and `GET /articles/1/delete` return 405 Method Not Allowed. `GET /articles` behaves as before.
- `IArticleService` and `IArticleRepository` are unchanged, and the repository queries use prepared statements.

## Out of Scope

- Create and edit forms, and any delete UI
- Input validation and error messages
- Styling
