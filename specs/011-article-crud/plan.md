# Plan: Article CRUD

Spec: [./spec.md](./spec.md)

Add a single-article HTML detail page (`GET /articles/{id}`) and three POST endpoints (create, update, delete) on `ArticleController`. To do this, implement the stub `get`/`create`/`update`/`delete` methods in `ArticleService` and `ArticleRepository`, add `ArticleViewModel` and an `articles/show.php` view, and add the view model to the project structure in `AGENTS.md`.

## Technical context

- PHP `>=8.5`; PSR-4 `App\` → `app/src/`. No `declare(strict_types=1)` in `app/src`.
- `app/public/index.php` has FastRoute with one route, `GET /articles` → `ArticleController::index`. It calls `$controller->$method($vars)`. Unmatched route → 404 `Not Found`, wrong method → 405 `Method Not Allowed` (both already handled there).
- Route variables arrive as strings in `$vars` (e.g. `$vars['id'] === '1'`). Cast them with `(int)`.
- `ArticleController` creates `ArticleService` in its constructor. `index()` builds `ArticlesViewModel('Articles', ...)` and `require`s `Views/articles/index.php`.
- `ArticleService` delegates `getAll()` to `ArticleRepository`. `get`/`create`/`update`/`delete` are empty stubs in both classes, and `get` returns `null`.
- `ArticleRepository` holds a `\PDO $pdo` (default emulated prepares, so ints come back as native ints in PHP 8.1+). `getAll()` uses `query()` + `fetchAll(\PDO::FETCH_CLASS, Article::class)`.
- `IArticleService` / `IArticleRepository` both declare `getAll(): array`, `create(Article): void`, `get(int): ?Article`, `update(Article): void`, `delete(int): void`. **Do not change them.**
- `App\Models\Article`: `?int $id = null`, `string $title`, `string $content`, `string $author`, `?string $created_at = null`. No constructor.
- `articles.created_at` has a DB default, so the endpoints never write it.
- `ArticlesViewModel` uses constructor promotion (`public string $title`, `public array $articles`). `ArticleViewModel` follows the same pattern.
- `partials/header.php` reads `$model->title`. `partials/article.php` reads `$article` (title `<h2>`, author, `created_at`, content). `partials/footer.php` has no variables.
- Seeded DB: 20 articles, ids 1–20.

## Target structure

- Change `app/public/index.php` (routes only)
- Change `app/src/Controllers/ArticleController.php` (add `show`, `store`, `update`, `destroy`; `index` unchanged)
- Change `app/src/Services/ArticleService.php` (implement stubs)
- Change `app/src/Repositories/ArticleRepository.php` (implement stubs)
- Add `app/src/ViewModels/ArticleViewModel.php`
- Add `app/src/Views/articles/show.php`
- Change `app/src/Views/partials/header.php` (`@var` DocBlock only)
- Change `AGENTS.md` (ViewModels line only)
- Unchanged: interfaces, model, `ArticlesViewModel`, `articles/index.php`, `partials/article.php`, `partials/footer.php`, migrations, seeds, Docker/Composer config, `README.md`

## Routes (`app/public/index.php`)

```php
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/articles', ['App\Controllers\ArticleController', 'index']);
    $r->addRoute('GET', '/articles/{id:\d+}', ['App\Controllers\ArticleController', 'show']);
    $r->addRoute('POST', '/articles/create', ['App\Controllers\ArticleController', 'store']);
    $r->addRoute('POST', '/articles/{id:\d+}/edit', ['App\Controllers\ArticleController', 'update']);
    $r->addRoute('POST', '/articles/{id:\d+}/delete', ['App\Controllers\ArticleController', 'destroy']);
});
```

- No GET routes for `/articles/create`, `/articles/{id}/edit` or `/articles/{id}/delete`. The dispatcher's existing METHOD_NOT_ALLOWED branch returns 405 for those.
- `GET /articles/create` does not match `{id:\d+}`, so it gets 405 (POST-only path), not 404.
- Rest of `index.php` (dispatch/switch) unchanged.

## Repository (`ArticleRepository`)

All queries use `prepare()` + bound parameters + `execute()`. No string interpolation of values.

```php
public function get(int $id): ?Article
{
    $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = :id');
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, Article::class);
    $article = $stmt->fetch();
    return $article ?: null;
}

public function create(Article $article): void
{
    $stmt = $this->pdo->prepare(
        'INSERT INTO articles (title, content, author) VALUES (:title, :content, :author)'
    );
    $stmt->bindValue(':title', $article->title);
    $stmt->bindValue(':content', $article->content);
    $stmt->bindValue(':author', $article->author);
    $stmt->execute();
}

public function update(Article $article): void
{
    $stmt = $this->pdo->prepare(
        'UPDATE articles SET title = :title, content = :content, author = :author WHERE id = :id'
    );
    $stmt->bindValue(':title', $article->title);
    $stmt->bindValue(':content', $article->content);
    $stmt->bindValue(':author', $article->author);
    $stmt->bindValue(':id', $article->id, \PDO::PARAM_INT);
    $stmt->execute();
}

public function delete(int $id): void
{
    $stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    $stmt->execute();
}
```

- `created_at` never appears in INSERT/UPDATE.
- `getAll()` and the constructor unchanged.

## Service (`ArticleService`)

Each method delegates one-to-one:

```php
public function create(Article $article): void { $this->articleRepository->create($article); }
public function get(int $id): ?Article { return $this->articleRepository->get($id); }
public function update(Article $article): void { $this->articleRepository->update($article); }
public function delete(int $id): void { $this->articleRepository->delete($id); }
```

## ArticleViewModel (`app/src/ViewModels/ArticleViewModel.php`)

```php
<?php

namespace App\ViewModels;

use App\Models\Article;

class ArticleViewModel
{
    public function __construct(
        public string $title,
        public Article $article,
    ) {
    }
}
```

- Exactly these two properties, with no methods or data access. Then run `docker compose run --rm php composer dump-autoload`.

## Controller (`ArticleController`)

Add `use App\Models\Article;` and `use App\ViewModels\ArticleViewModel;`. Keep the constructor and `index` as they are.

```php
public function show($vars = [])
{
    $article = $this->articleService->get((int) $vars['id']);
    if ($article === null) {
        http_response_code(404);
        echo 'Not Found';
        return;
    }
    $model = new ArticleViewModel($article->title, $article);
    require __DIR__ . '/../Views/articles/show.php';
}

public function store($vars = [])
{
    $article = new Article();
    $article->title = $_POST['title'] ?? '';
    $article->content = $_POST['content'] ?? '';
    $article->author = $_POST['author'] ?? '';
    $this->articleService->create($article);
    header('Location: /articles', true, 302);
}

public function update($vars = [])
{
    $article = $this->articleService->get((int) $vars['id']);
    if ($article === null) {
        http_response_code(404);
        echo 'Not Found';
        return;
    }
    $article->title = $_POST['title'] ?? '';
    $article->content = $_POST['content'] ?? '';
    $article->author = $_POST['author'] ?? '';
    $this->articleService->update($article);
    header('Location: /articles', true, 302);
}

public function destroy($vars = [])
{
    $id = (int) $vars['id'];
    if ($this->articleService->get($id) === null) {
        http_response_code(404);
        echo 'Not Found';
        return;
    }
    $this->articleService->delete($id);
    header('Location: /articles', true, 302);
}
```

- The 404 checks happen in the controller through `get()`, because the interfaces return `void` and must not change.
- No validation, no extra helper methods, no base controller. The 404 body is exactly `Not Found`, matching `index.php`.
- Redirect responses have no body. Nothing runs after `header(...)`, so `exit` is not needed.

## Views

`app/src/Views/articles/show.php`:

```php
<?php
/** @var \App\ViewModels\ArticleViewModel $model */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main>
    <?php $article = $model->article; ?>
    <?php require __DIR__ . '/../partials/article.php'; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
```

`app/src/Views/partials/header.php`: change only the DocBlock to:

```php
/** @var \App\ViewModels\ArticlesViewModel|\App\ViewModels\ArticleViewModel $model */
```

- `partials/article.php` is unchanged and already escapes everything. No CSS, `style` or `class` attributes.

## AGENTS.md

- In Project structure, change `` `app/src/ViewModels/`: `ArticlesViewModel` `` → `` `app/src/ViewModels/`: `ArticlesViewModel`, `ArticleViewModel` ``. No other edits.

## Implementation order

1. Implement the repository methods.
2. Implement the service delegation.
3. Create `ArticleViewModel`, then run `docker compose run --rm php composer dump-autoload`.
4. Create `articles/show.php` and update the `header.php` DocBlock.
5. Add controller methods `show`, `store`, `update`, `destroy`.
6. Add the four routes in `index.php`.
7. Update `AGENTS.md`.
8. Verify (below).

## Verification

Run against `http://localhost` with the stack up and the DB seeded. **Use a throwaway row for the write checks. Do not edit or delete the seed articles (ids 1–20), and do not reseed or truncate.**

- `curl -i http://localhost/articles/1` → 200; `<title>` and `<h1>` are article 1's title; the page shows title, author, `created_at` and content.
- `curl -i http://localhost/articles/9999` → 404, body `Not Found`.
- `curl -i -X POST -d 'title=CRUD test&content=Test body&author=Tester' http://localhost/articles/create` → 302, `Location: /articles`. Find the new id (highest id, e.g. on `/articles` or in phpMyAdmin). Check that `created_at` is set and the article appears on `/articles`.
- Note that row's `created_at`, then `curl -i -X POST -d 'title=CRUD test edited&content=Edited body&author=Tester 2' http://localhost/articles/<newId>/edit` → 302 to `/articles`. Title, content and author are changed, and `created_at` is the same as before.
- `curl -i -X POST http://localhost/articles/<newId>/delete` → 302 to `/articles`. The row is gone, and `GET /articles/<newId>` → 404.
- `curl -i -X POST -d 'title=x&content=x&author=x' http://localhost/articles/9999/edit` and `curl -i -X POST http://localhost/articles/9999/delete` → 404 `Not Found`, and the row count is unchanged (still 20).
- `curl -i http://localhost/articles/create`, `.../articles/1/edit`, `.../articles/1/delete` (GET) → 405.
- `GET /articles` lists the 20 seed articles as before.
- `git diff` shows no changes to `IArticleService.php` / `IArticleRepository.php`.
- No PHP warnings or notices in any response.

## Out of scope

- Create and edit forms, GET form pages, delete UI or links
- Input validation, error messages, CSRF protection, flash messages
- Styling or CSS
- HTML error pages for 404/405 (existing TODO in `index.php`)
- Changes to interfaces, model, migrations, seeds, `README.md`
- Links from the list page to detail pages
