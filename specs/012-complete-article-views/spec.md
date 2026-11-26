## Goal

Complete the article frontend: add create and edit form pages, a delete button and navigation links, so every article CRUD action from spec 011 can be done in the browser.

## Requirements

- New GET routes in `app/public/index.php` (FastRoute), handled by new methods on `App\Controllers\ArticleController`:
  - `GET /articles/create` → `create`
  - `GET /articles/{id}/edit` → `edit`
- Existing routes, POST endpoints (`store`, `update`, `destroy`, which all redirect to `/articles`), service, repository, interfaces, model and `ArticleViewModel` are unchanged.
- Both form pages reuse `App\ViewModels\ArticleViewModel`:
  - `create` assigns `$model = new ArticleViewModel('New article', new Article())` and `require`s `app/src/Views/articles/create.php`.
  - `edit` loads the article with `get((int) $vars['id'])`. If it is `null`, it responds with HTTP 404 and `Not Found`, like `show`. Otherwise it assigns `$model = new ArticleViewModel('Edit article', $article)` and `require`s `app/src/Views/articles/edit.php`.
- `articles/create.php` and `articles/edit.php` are typed with `/** @var \App\ViewModels\ArticleViewModel $model */`. Each includes `partials/header.php`, a `Back to articles` link to `/articles`, `partials/article-form.php` (with `$article = $model->article`) and `partials/footer.php`.
- New shared partial `app/src/Views/partials/article-form.php`, typed with `/** @var \App\Models\Article $article */`:
  - `<form method="post">` with a `<label>` for each field: `title` (`<input type="text">`), `content` (`<textarea>`) and `author` (`<input type="text">`). All three have the HTML `required` attribute.
  - If `$article->id` is `null` (create), the form posts to `/articles/create`, the fields are empty and the submit button reads `Create`.
  - Otherwise (edit), the form posts to `/articles/{id}/edit`, the fields are pre-filled with the article's current values, the submit button reads `Save`, and a `Cancel` link goes to `/articles/{id}`.
  - Reading `title`, `content` and `author` must not raise errors on a new `Article`, whose properties are uninitialized (e.g. use `?? ''`).
- `partials/article.php`: the `<h2>` title links to `/articles/{id}`, on both the list and the detail page.
- `articles/index.php`: a `New article` link to `/articles/create`. No Edit or Delete controls on the list.
- `articles/show.php`: below the article, an `Edit` link to `/articles/{id}/edit`, a `Delete` button (a `<form method="post" action="/articles/{id}/delete">` with a submit button) and a `Back to articles` link to `/articles`. No JavaScript confirmation.
- Follow the `AGENTS.md` conventions: views and partials carry `@var` DocBlocks; controllers assign `$model` and `require` the view; no render helper or base controller; all output (including URLs and form values) escaped with `htmlspecialchars`; no CSS, `style` or `class` attributes.

## Acceptance Criteria

- `GET /articles` shows a `New article` link to `/articles/create`, and each article title links to its detail page.
- `GET /articles/create` returns an HTML page with `<title>` and heading `New article`, an empty form with labelled, required `title`, `content` (textarea) and `author` fields, a `Create` button, and a `Back to articles` link.
- Submitting the create form adds the article and redirects to `/articles`, where it is listed.
- `GET /articles/{id}` for an existing article shows `Edit`, `Delete` and `Back to articles` controls.
- `GET /articles/{id}/edit` for an existing article returns a page with `<title>` and heading `Edit article`, the form pre-filled with that article's values, a `Save` button, and `Cancel` (to `/articles/{id}`) and `Back to articles` links.
- `GET /articles/9999/edit` (no such article) returns 404 `Not Found`.
- Submitting the edit form updates the article and redirects to `/articles`.
- Clicking `Delete` on the detail page removes the article (no confirmation) and redirects to `/articles`.
- Values containing HTML (e.g. a title `<b>x</b>`) are shown escaped in the list, detail page and edit form.
- No PHP warnings or notices on any of the pages.

## Out of Scope

- ...
