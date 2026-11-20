# Plan: Complete article views

Spec: [./spec.md](./spec.md)

Add `GET /articles/create` and `GET /articles/{id}/edit` form pages (new `create`/`edit` controller methods, two views, one shared `article-form.php` partial), link article titles to their detail page, add a `New article` link to the list, and add `Edit`/`Delete`/`Back to articles` controls to the detail page.

## Technical context

- PHP `>=8.5`; PSR-4 `App\` → `app/src/`. No new classes, so no `dump-autoload` needed.
- `app/public/index.php` routes (unchanged except for the two additions): `GET /articles` → `index`, `GET /articles/{id}` → `show`, `POST /articles/create` → `store`, `POST /articles/{id}/edit` → `update`, `POST /articles/{id}/delete` → `destroy`. Route variables use plain `{id}` (no regex); keep that style for the new route.
- FastRoute matches static routes before variable ones, so `GET /articles/create` hits `create`, not `show`.
- `$vars['id']` is a string; cast with `(int)`.
- `ArticleController::show` already does `get((int) $vars['id'])` → `http_response_code(404); echo 'Not Found'; return;` when `null`. `store`/`update`/`destroy` already redirect 302 to `/articles`. **Do not change them.**
- `App\Models\Article`: `?int $id = null`, `string $title`, `string $content`, `string $author`, `?string $created_at = null`. On `new Article()`, `title`/`content`/`author` are uninitialized; `$article->title ?? ''` reads them without error.
- `ArticleViewModel(string $title, Article $article)`; `partials/header.php` already types `$model` as `ArticlesViewModel|ArticleViewModel` and prints `$model->title` in `<title>` and `<h1>`. No change needed.
- `partials/article.php` expects `$article`; used by `articles/index.php` (in a loop) and `articles/show.php`.
- Seeded DB: 20 articles, ids 1–20.

## Target structure

- Change `app/public/index.php` (two routes)
- Change `app/src/Controllers/ArticleController.php` (add `create`, `edit`)
- Add `app/src/Views/articles/create.php`
- Add `app/src/Views/articles/edit.php`
- Add `app/src/Views/partials/article-form.php`
- Change `app/src/Views/partials/article.php` (title link)
- Change `app/src/Views/articles/index.php` (`New article` link)
- Change `app/src/Views/articles/show.php` (controls)
- Unchanged: existing routes, `index`/`show`/`store`/`update`/`destroy`, service, repository, interfaces, model, view models, `header.php`, `footer.php`, `AGENTS.md`

## Routes (`app/public/index.php`)

```php
$r->addRoute('GET', '/articles', ['App\Controllers\ArticleController', 'index']);
$r->addRoute('GET', '/articles/create', ['App\Controllers\ArticleController', 'create']);
$r->addRoute('GET', '/articles/{id}', ['App\Controllers\ArticleController', 'show']);
$r->addRoute('GET', '/articles/{id}/edit', ['App\Controllers\ArticleController', 'edit']);
// existing POST routes unchanged
```

## Controller (`ArticleController`)

`use` statements already include `Article` and `ArticleViewModel`.

```php
public function create($vars = [])
{
    $model = new ArticleViewModel('New article', new Article());
    require __DIR__ . '/../Views/articles/create.php';
}

public function edit($vars = [])
{
    $article = $this->articleService->get((int) $vars['id']);
    if ($article === null) {
        http_response_code(404);
        echo 'Not Found';
        return;
    }
    $model = new ArticleViewModel('Edit article', $article);
    require __DIR__ . '/../Views/articles/edit.php';
}
```

- Place `create` after `index` and `edit` after `show`. No helpers, no base controller.

## Form partial (`app/src/Views/partials/article-form.php`)

```php
<?php
/** @var \App\Models\Article $article */
$isNew = $article->id === null;
$action = $isNew ? '/articles/create' : '/articles/' . $article->id . '/edit';
?>
<form method="post" action="<?= htmlspecialchars($action) ?>">
    <p>
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($article->title ?? '') ?>" required>
    </p>
    <p>
        <label for="content">Content</label>
        <textarea id="content" name="content" required><?= htmlspecialchars($article->content ?? '') ?></textarea>
    </p>
    <p>
        <label for="author">Author</label>
        <input type="text" id="author" name="author" value="<?= htmlspecialchars($article->author ?? '') ?>" required>
    </p>
    <p>
        <?php if ($isNew): ?>
            <button type="submit">Create</button>
        <?php else: ?>
            <button type="submit">Save</button>
            <a href="<?= htmlspecialchars('/articles/' . $article->id) ?>">Cancel</a>
        <?php endif; ?>
    </p>
</form>
```

- No whitespace between `<textarea>` tags and the value (it would become part of the content).
- Create mode yields empty fields because the model's properties are uninitialized (`?? ''`).

## Form views

`app/src/Views/articles/create.php` and `app/src/Views/articles/edit.php` have the same markup (the heading comes from `$model->title`):

```php
<?php
/** @var \App\ViewModels\ArticleViewModel $model */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main>
    <p><a href="/articles">Back to articles</a></p>
    <?php $article = $model->article; ?>
    <?php require __DIR__ . '/../partials/article-form.php'; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
```

- Keep two separate files (`create.php`, `edit.php`) as the spec requires, even though their content is the same.

## Existing views

`partials/article.php`, title becomes a link (used by list and detail page):

```php
<h2><a href="<?= htmlspecialchars('/articles/' . $article->id) ?>"><?= htmlspecialchars($article->title) ?></a></h2>
```

`articles/index.php`, first line inside `<main>` (shown even when the list is empty):

```php
<p><a href="/articles/create">New article</a></p>
```

`articles/show.php`, after the `article.php` require, inside `<main>`:

```php
<p><a href="<?= htmlspecialchars('/articles/' . $article->id . '/edit') ?>">Edit</a></p>
<form method="post" action="<?= htmlspecialchars('/articles/' . $article->id . '/delete') ?>">
    <button type="submit">Delete</button>
</form>
<p><a href="/articles">Back to articles</a></p>
```

- No JavaScript (no `onsubmit`/`confirm`). No Edit/Delete controls on the list.
- No CSS, `style` or `class` attributes anywhere.

## Implementation order

1. Add `partials/article-form.php`.
2. Add `articles/create.php` and `articles/edit.php`.
3. Add controller methods `create` and `edit`.
4. Add the two GET routes.
5. Update `partials/article.php`, `articles/index.php`, `articles/show.php`.
6. Verify (below).

## Verification

Run against `http://localhost` with the stack up and the DB seeded. **Use a throwaway row for write checks. Do not edit or delete seed articles (ids 1–20), and do not reseed or truncate.**

- `GET /articles` → `New article` link to `/articles/create`; each title links to `/articles/{id}`.
- `GET /articles/create` → 200; `<title>` and `<h1>` `New article`; empty form posting to `/articles/create` with labelled, `required` `title`, `content` (textarea), `author`; `Create` button; `Back to articles` link.
- Submit the create form in a browser with title `<b>x</b>` → redirected to `/articles`, new article listed with the title shown as literal `<b>x</b>`.
- `GET /articles/<newId>` → title literal, `Edit`, `Delete`, `Back to articles` present.
- `GET /articles/<newId>/edit` → `<title>`/`<h1>` `Edit article`; fields pre-filled (title shows `<b>x</b>` escaped in the `value`); `Save` button; `Cancel` → `/articles/<newId>`; `Back to articles`.
- Submit the edit form with changed values → redirected to `/articles`, changes shown.
- Click `Delete` on `/articles/<newId>` → no confirmation, redirected to `/articles`, row gone.
- `GET /articles/9999/edit` → 404 `Not Found`.
- `GET /articles/1/delete` → still 405.
- No PHP warnings or notices on any page (including the create page with uninitialized properties).
- `git diff` shows no changes to the service, repository, interfaces, model, view models or existing controller methods.

## Out of scope

- Input validation, error messages, CSRF protection, flash messages
- Delete confirmation (JavaScript or otherwise)
- Edit/Delete controls on the list page
- Styling or CSS
- HTML error pages for 404/405
- Changes to POST endpoints, redirects, service, repository, interfaces, model, view models, `AGENTS.md`
