# Tasks: Article view model and view

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## View model

- [x] Create `app/src/ViewModels/ArticlesViewModel.php` with `namespace App\ViewModels;` and `class ArticlesViewModel`
- [x] Constructor promotion with exactly `public string $title` and `public array $articles`; `articles` documented as `\App\Models\Article[]`
- [x] No other properties or methods, no data access
- [x] Run `docker compose run --rm php composer dump-autoload`

## Views

- [x] `app/src/Views/articles/index.php`: starts with `/** @var \App\ViewModels\ArticlesViewModel $model */`
- [x] `index.php` requires `../partials/header.php` first and `../partials/footer.php` last (`__DIR__`-relative)
- [x] `index.php` requires `../partials/article.php` once per item in `foreach ($model->articles as $article)`
- [x] `index.php` shows `<p>No articles found.</p>` when `$model->articles` is empty
- [x] `partials/header.php`: doctype, `<html lang="en">`, `<head>` with `<meta charset="utf-8">` and `<title>` = `$model->title`, opening `<body>`, `<header><h1>` = `$model->title`
- [x] `partials/footer.php`: `<footer>` with one short text line, then `</body></html>`
- [x] `partials/article.php`: starts with `/** @var \App\Models\Article $article */`; `<article>` with title (`<h2>`), author, `created_at` as stored, full content
- [x] Every output value wrapped in `htmlspecialchars`
- [x] No CSS, `<style>`, `style` or `class` attributes

## Controller

- [x] `ArticleController::index`: `$model = new ArticlesViewModel('Articles', $this->articleService->getAll());` then `require __DIR__ . '/../Views/articles/index.php';`
- [x] Remove `var_dump`; add `use App\ViewModels\ArticlesViewModel;`
- [x] No render helper, no base controller; route, model, service, repository unchanged

## Docs

- [x] `README.md`: replace `HelloController` / `GET /hello/{name}` sentence with `GET /articles` → `ArticleController::index`
- [x] `README.md` `### NGINX`: replace "Welcome home!" line with link to `http://localhost/articles`
- [x] `README.md`: add `### Views` after `### Models` with the view conventions from the plan
- [x] `AGENTS.md` Project structure: Controllers → `ArticleController`; add `app/src/Services/`, `app/src/Repositories/`, `app/src/ViewModels/`, `app/src/Views/` lines
- [x] `AGENTS.md` Request flow: replace `HelloController` example with the `/articles` route; keep note that route variables arrive in `$vars`
- [x] `AGENTS.md`: add `## Views` after `## Models` with the view conventions from the plan

## Verify

- [x] DB migrated and seeded (only run `phinx seed:run -s ArticlesSeeder` if the 20 seed rows are missing)
- [x] `http://localhost/articles` returns a complete HTML document, no `var_dump` output
- [x] `<title>` and `<h1>` are `Articles`
- [x] Page shows 20 `<article>` elements, each with title, author, `created_at` and content
- [x] No PHP warnings/notices in the output
- [x] Do not truncate or delete data; leave the empty-table (`No articles found.`) check to the user
- [x] `git status` shows only the new ViewModels/Views files, `ArticleController.php`, `README.md`, `AGENTS.md` (plus the spec folder)
