# Tasks: Complete article views

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Form partial

- [ ] Create `app/src/Views/partials/article-form.php` with `/** @var \App\Models\Article $article */`
- [ ] `<form method="post">`; action `/articles/create` when `$article->id === null`, otherwise `/articles/{id}/edit` (escaped)
- [ ] Labelled fields with `id`/`name` `title` (`<input type="text">`), `content` (`<textarea>`), `author` (`<input type="text">`), all `required`
- [ ] Values read with `?? ''` and escaped with `htmlspecialchars`; no whitespace inside `<textarea>` around the value
- [ ] Create mode: `Create` submit button. Edit mode: `Save` submit button and `Cancel` link to `/articles/{id}`

## Form views

- [ ] Create `app/src/Views/articles/create.php` and `app/src/Views/articles/edit.php`, each with `/** @var \App\ViewModels\ArticleViewModel $model */`
- [ ] Each: `header.php`, then in `<main>` a `Back to articles` link to `/articles`, `$article = $model->article;`, `article-form.php`, then `footer.php`

## Controller

- [ ] `create`: `$model = new ArticleViewModel('New article', new Article())`, `require` `Views/articles/create.php`
- [ ] `edit`: `get((int) $vars['id'])`; if `null`, 404 + `Not Found` and return; otherwise `$model = new ArticleViewModel('Edit article', $article)`, `require` `Views/articles/edit.php`
- [ ] Do not change `index`, `show`, `store`, `update`, `destroy` or the constructor; no render helper or base controller

## Routes

- [ ] `app/public/index.php`: add `GET /articles/create` → `create` and `GET /articles/{id}/edit` → `edit`
- [ ] Existing routes unchanged

## Existing views

- [ ] `partials/article.php`: wrap the `<h2>` title in a link to `/articles/{id}` (escaped)
- [ ] `articles/index.php`: `New article` link to `/articles/create` at the top of `<main>`; no Edit/Delete controls on the list
- [ ] `articles/show.php`: after the article, `Edit` link to `/articles/{id}/edit`, `<form method="post" action="/articles/{id}/delete">` with a `Delete` submit button, and a `Back to articles` link
- [ ] No JavaScript confirmation; no CSS, `style` or `class` attributes; all output escaped with `htmlspecialchars`

## Verify

- [ ] Do not edit or delete seed articles (ids 1–20); do not reseed or truncate. Use one throwaway row
- [ ] `GET /articles`: `New article` link; each title links to its detail page
- [ ] `GET /articles/create`: `<title>`/`<h1>` `New article`; empty labelled required fields; `Create` button; `Back to articles` link
- [ ] Create an article with title `<b>x</b>` via the form → redirect to `/articles`, listed with the title shown escaped
- [ ] `GET /articles/<newId>`: title escaped; `Edit`, `Delete`, `Back to articles` present
- [ ] `GET /articles/<newId>/edit`: `<title>`/`<h1>` `Edit article`; fields pre-filled and escaped; `Save`, `Cancel` (→ `/articles/<newId>`), `Back to articles`
- [ ] Submit the edit form → redirect to `/articles`, changes shown
- [ ] `Delete` on the detail page → no confirmation, redirect to `/articles`, row gone
- [ ] `GET /articles/9999/edit` → 404 `Not Found`
- [ ] No PHP warnings or notices on any page
- [ ] `git diff`: service, repository, interfaces, model and view models unchanged
