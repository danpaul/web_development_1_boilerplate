# Tasks: Complete article views

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Form partial

- [x] Create `app/src/Views/partials/article-form.php` with `/** @var \App\Models\Article $article */`
- [x] `<form method="post">`; action `/articles/create` when `$article->id === null`, otherwise `/articles/{id}/edit` (escaped)
- [x] Labelled fields with `id`/`name` `title` (`<input type="text">`), `content` (`<textarea>`), `author` (`<input type="text">`), all `required`
- [x] Values read with `?? ''` and escaped with `htmlspecialchars`; no whitespace inside `<textarea>` around the value
- [x] Create mode: `Create` submit button. Edit mode: `Save` submit button and `Cancel` link to `/articles/{id}`

## Form views

- [x] Create `app/src/Views/articles/create.php` and `app/src/Views/articles/edit.php`, each with `/** @var \App\ViewModels\ArticleViewModel $model */`
- [x] Each: `header.php`, then in `<main>` a `Back to articles` link to `/articles`, `$article = $model->article;`, `article-form.php`, then `footer.php`

## Controller

- [x] `create`: `$model = new ArticleViewModel('New article', new Article())`, `require` `Views/articles/create.php`
- [x] `edit`: `get((int) $vars['id'])`; if `null`, 404 + `Not Found` and return; otherwise `$model = new ArticleViewModel('Edit article', $article)`, `require` `Views/articles/edit.php`
- [x] Do not change `index`, `show`, `store`, `update`, `destroy` or the constructor; no render helper or base controller

## Routes

- [x] `app/public/index.php`: add `GET /articles/create` → `create` and `GET /articles/{id}/edit` → `edit`
- [x] Existing routes unchanged

## Existing views

- [x] `partials/article.php`: wrap the `<h2>` title in a link to `/articles/{id}` (escaped)
- [x] `articles/index.php`: `New article` link to `/articles/create` at the top of `<main>`; no Edit/Delete controls on the list
- [x] `articles/show.php`: after the article, `Edit` link to `/articles/{id}/edit`, `<form method="post" action="/articles/{id}/delete">` with a `Delete` submit button, and a `Back to articles` link
- [x] No JavaScript confirmation; no CSS, `style` or `class` attributes; all output escaped with `htmlspecialchars`

## Verify

- [x] Do not edit or delete seed articles (ids 1–20); do not reseed or truncate. Use one throwaway row
- [x] `GET /articles`: `New article` link; each title links to its detail page
- [x] `GET /articles/create`: `<title>`/`<h1>` `New article`; empty labelled required fields; `Create` button; `Back to articles` link
- [x] Create an article with title `<b>x</b>` via the form → redirect to `/articles`, listed with the title shown escaped
- [x] `GET /articles/<newId>`: title escaped; `Edit`, `Delete`, `Back to articles` present
- [x] `GET /articles/<newId>/edit`: `<title>`/`<h1>` `Edit article`; fields pre-filled and escaped; `Save`, `Cancel` (→ `/articles/<newId>`), `Back to articles`
- [x] Submit the edit form → redirect to `/articles`, changes shown
- [x] `Delete` on the detail page → no confirmation, redirect to `/articles`, row gone
- [x] `GET /articles/9999/edit` → 404 `Not Found`
- [x] No PHP warnings or notices on any page
- [x] `git diff`: service, repository, interfaces, model and view models unchanged
