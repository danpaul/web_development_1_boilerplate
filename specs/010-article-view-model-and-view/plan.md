# Plan: Article view model and view

Spec: [./spec.md](./spec.md)

Replace the `var_dump` in `ArticleController::index` with an HTML page: build an `App\ViewModels\ArticlesViewModel`, assign it to `$model`, and `require` a plain PHP view that includes header, footer and per-article partials. Document the view conventions in `README.md` and `AGENTS.md`, and fix their stale controller references.

## Technical context

- PHP `>=8.5`; PSR-4 `App\` → `app/src/` (`app/composer.json`). No `declare(strict_types=1)` in `app/src` classes.
- `app/public/index.php` routes `GET /articles` → `App\Controllers\ArticleController::index($vars)`. Only route; `/` returns 404.
- `ArticleController` creates `ArticleService` in its constructor; `index()` currently does `var_dump($this->articleService->getAll())`.
- `ArticleService::getAll()` → `ArticleRepository::getAll()` → `SELECT * FROM articles` with `PDO::FETCH_CLASS, Article::class` (no `ORDER BY`; order is whatever the DB returns, unchanged).
- `App\Models\Article`: `?int $id`, `string $title`, `string $content`, `string $author`, `?string $created_at`.
- Seed data: 20 articles; content is a single paragraph of plain text.
- `include`/`require` share the including scope, so `$model` from the controller method is visible in `articles/index.php` and the header/footer partials, and the `foreach` variable `$article` is visible in `partials/article.php`.
- Existing stale docs: `AGENTS.md` project structure lists `HomeController`, `HelloController` (do not exist) and omits `app/src/Services/`, `app/src/Repositories/`; its Request flow example uses `HelloController`. `README.md` mentions `GET /hello/{name}` / `HelloController` and "Welcome home!" from `HomeController::home`.

## Target structure

- Add `app/src/ViewModels/ArticlesViewModel.php`
- Add `app/src/Views/articles/index.php`
- Add `app/src/Views/partials/header.php`
- Add `app/src/Views/partials/footer.php`
- Add `app/src/Views/partials/article.php`
- Change `app/src/Controllers/ArticleController.php` (`index` only)
- Change `README.md`, `AGENTS.md`
- Unchanged: `app/public/index.php`, model, service, repository, interfaces, migrations, seeders, Composer/Docker config

## ArticlesViewModel

```php
<?php

namespace App\ViewModels;

class ArticlesViewModel
{
    /**
     * @param \App\Models\Article[] $articles
     */
    public function __construct(
        public string $title,
        /** @var \App\Models\Article[] */
        public array $articles,
    ) {
    }
}
```

- Exactly two properties, `title` and `articles`, via constructor promotion. No other methods, no defaults, no data access.

Then run:

```sh
docker compose run --rm php composer dump-autoload
```

## ArticleController::index

```php
use App\ViewModels\ArticlesViewModel;

public function index($vars = [])
{
    $articles = $this->articleService->getAll();
    $model = new ArticlesViewModel('Articles', $articles);
    require __DIR__ . '/../Views/articles/index.php';
}
```

- No render helper, no base controller. Constructor and existing `use` lines unchanged.

## Views

All files start with `<?php` + `@var` DocBlock (where listed), then plain HTML. Every output value goes through `htmlspecialchars(...)`. No CSS, no `style`/`class` attributes. Use `__DIR__`-relative paths for includes.

`app/src/Views/articles/index.php`:

```php
<?php
/** @var \App\ViewModels\ArticlesViewModel $model */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main>
    <?php if (empty($model->articles)): ?>
        <p>No articles found.</p>
    <?php else: ?>
        <?php foreach ($model->articles as $article): ?>
            <?php require __DIR__ . '/../partials/article.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
```

`app/src/Views/partials/header.php` (uses `$model` from the including scope; no `@var` required by spec, but add `/** @var \App\ViewModels\ArticlesViewModel $model */` for editor support):

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($model->title) ?></title>
</head>
<body>
<header>
    <h1><?= htmlspecialchars($model->title) ?></h1>
</header>
```

`app/src/Views/partials/footer.php`:

```php
<footer>
    <p>Web Development 1 demo</p>
</footer>
</body>
</html>
```

`app/src/Views/partials/article.php`:

```php
<?php
/** @var \App\Models\Article $article */
?>
<article>
    <h2><?= htmlspecialchars($article->title) ?></h2>
    <p>By <?= htmlspecialchars($article->author) ?> on <time><?= htmlspecialchars($article->created_at ?? '') ?></time></p>
    <p><?= htmlspecialchars($article->content) ?></p>
</article>
```

- `created_at` printed as stored (e.g. `2026-10-01 09:00:00`), no date formatting.

## README.md

- Replace the stale `HelloController` sentence under `### Composer Autoload` with: routes are defined in `app/public/index.php`; `GET /articles` calls `ArticleController::index` (`app/src/Controllers/ArticleController.php`); controllers live in `app/src/Controllers/`.
- `### NGINX`: replace the "Welcome home!" line with: go to [http://localhost/articles](http://localhost/articles) to see the article list (requires migrations and seed).
- Add `### Views` after `### Models` (before `### NGINX`):
  - View models live in `app/src/ViewModels/` under `App\ViewModels`, hold data only, and are passed to the view as `$model` (example `ArticlesViewModel` with `title`, `articles`).
  - Views live in `app/src/Views/<area>/` (e.g. `articles/index.php`); partials in `app/src/Views/partials/` (`header.php`, `footer.php`, `article.php`).
  - Views and partials declare their variables with `/** @var ... */` DocBlocks at the top.
  - All output is escaped with `htmlspecialchars`.
  - Controllers render by assigning `$model` and `require`-ing the view. Short example: the `index()` sketch above.

## AGENTS.md

- Project structure:
  - Change `` `app/src/Controllers/`: `HomeController`, `HelloController` `` → `` `app/src/Controllers/`: `ArticleController` ``
  - Add `` `app/src/Services/`: `IArticleService`, `ArticleService` `` and `` `app/src/Repositories/`: `IArticleRepository`, `ArticleRepository` ``
  - Add `` `app/src/ViewModels/`: `ArticlesViewModel` `` and `` `app/src/Views/`: views per area (`articles/`) and shared `partials/` ``
- Request flow: replace the `HelloController` example with the real route and controller:

  ```php
  // app/public/index.php
  $r->addRoute('GET', '/articles', ['App\Controllers\ArticleController', 'index']);
  ```

  and keep a one-line note that route variables (e.g. `/{id}`) arrive in `$vars`.
- Add `## Views` section after `## Models`:
  - View models live in `app/src/ViewModels/` under `App\ViewModels`, are data only, and are passed to the view as `$model`.
  - Views live in `app/src/Views/<area>/`, partials in `app/src/Views/partials/`.
  - Views and partials type their variables with `/** @var ... */` DocBlocks.
  - Controllers render by assigning `$model` and `require`-ing the view (`require __DIR__ . '/../Views/<area>/<view>.php'`); no render helper or base controller.
  - Escape all output with `htmlspecialchars`; no CSS.

## Implementation order

1. Create `app/src/ViewModels/ArticlesViewModel.php`.
2. `docker compose run --rm php composer dump-autoload`.
3. Create the four view files.
4. Update `ArticleController::index`.
5. Check `http://localhost/articles` with the seeded DB (view source: complete HTML, title/heading `Articles`, 20 `<article>` elements, no `var_dump`).
6. Update `README.md` and `AGENTS.md`.

## Verification notes

- Seeded check is done by the implementer (run `phinx seed:run -s ArticlesSeeder` only if `articles` does not already hold the 20 seed rows).
- Empty-table check (`No articles found.`) is left to the user; the implementer must not truncate or delete data.

## Out of scope

- Styling, CSS, layout templates, render helpers, base controller, template engine
- Single-article page, create/edit/delete views, other routes
- Changes to route, model, service, repository, interfaces, DB schema or seeds
- HTML error pages for 404/405 (existing TODO in `index.php`)
- Date formatting, sorting or pagination
