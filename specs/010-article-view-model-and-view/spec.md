## Goal

Render the articles list at `GET /articles` as an HTML page built from a simple view model and plain PHP views, replacing the current `var_dump`. Keep it simple; this is for demo purposes.

## Requirements

- Add `App\ViewModels\ArticlesViewModel` in `app/src/ViewModels/ArticlesViewModel.php`, autoloaded through the existing PSR-4 mapping (`App\` → `app/src/`). Run `composer dump-autoload` after adding it.
- `ArticlesViewModel` holds data only:
  - `public string $title`: the page title (`Articles`)
  - `public array $articles`: the articles to show, documented with `/** @var \App\Models\Article[] */`
- Views are plain PHP templates in `app/src/Views/`:
  - `app/src/Views/articles/index.php`: the articles list view
  - `app/src/Views/partials/header.php`: shared header partial
  - `app/src/Views/partials/footer.php`: shared footer partial
  - `app/src/Views/partials/article.php`: renders one article
- Views declare the variables they receive with PHP DocBlock annotations at the top of the file:
  - `articles/index.php`: `/** @var \App\ViewModels\ArticlesViewModel $model */`
  - `partials/article.php`: `/** @var \App\Models\Article $article */`
- `articles/index.php` includes `partials/header.php` and `partials/footer.php`, and includes `partials/article.php` once per article in `$model->articles`. If `$model->articles` is empty, it shows `No articles found.` instead.
- `partials/header.php` outputs the start of the HTML document: doctype, `<head>` with `<title>` set to `$model->title`, the opening `<body>`, and a simple `<header>` showing `$model->title` as the heading.
- `partials/footer.php` outputs a simple `<footer>` with a short text line, then closes `</body></html>`.
- `partials/article.php` shows the article's title, author, `created_at` (as stored, e.g. `2026-10-01 10:00:00`) and full content.
- All output values are escaped with `htmlspecialchars`.
- No styling: plain semantic HTML, no CSS or CSS framework.
- `ArticleController::index` gets the articles from `ArticleService::getAll()`, builds an `ArticlesViewModel`, assigns it to `$model`, and `require`s `app/src/Views/articles/index.php`. No render helper or base controller class.
- Existing route (`GET /articles`), model, service and repository are unchanged.
- Update `README.md` and `AGENTS.md` with the view conventions:
  - View models live in `app/src/ViewModels/` under `App\ViewModels`, are data only, and are passed to the view as `$model`.
  - Views live in `app/src/Views/<area>/`, partials in `app/src/Views/partials/`.
  - Views and partials type their variables with `/** @var ... */` DocBlocks.
  - Controllers render by assigning `$model` and `require`-ing the view.
  - Add `app/src/ViewModels/` and `app/src/Views/` to the project structure in `AGENTS.md`.

## Acceptance Criteria

- With the seeded database, `http://localhost/articles` returns a complete HTML page (no `var_dump` output) whose `<title>` and heading are `Articles`.
- The page lists all 20 seeded articles, each showing its title, author, `created_at` and content.
- The header and footer come from `partials/header.php` and `partials/footer.php`, and each article is rendered by `partials/article.php`.
- With an empty `articles` table, the page shows `No articles found.`
- `app/src/ViewModels/ArticlesViewModel.php` defines `App\ViewModels\ArticlesViewModel` with exactly `title` and `articles`, and the views carry the `@var` DocBlocks listed above.
- `README.md` and `AGENTS.md` describe the view conventions above.

## Out of Scope

-
