# Tasks: Article CRUD

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Repository

- [x] `ArticleRepository::get`: prepared `SELECT * FROM articles WHERE id = :id`, `:id` bound as `PARAM_INT`, `setFetchMode(\PDO::FETCH_CLASS, Article::class)`, return the article or `null`
- [x] `ArticleRepository::create`: prepared `INSERT` of `title`, `content`, `author` only
- [x] `ArticleRepository::update`: prepared `UPDATE ... SET title, content, author WHERE id = :id`
- [x] `ArticleRepository::delete`: prepared `DELETE FROM articles WHERE id = :id`
- [x] All values bound as parameters, with no interpolation; `created_at` never written
- [x] `getAll()`, constructor and `IArticleRepository` unchanged

## Service

- [x] `ArticleService::get`, `create`, `update`, `delete` delegate to the repository
- [x] `IArticleService` unchanged

## View model and views

- [x] Create `app/src/ViewModels/ArticleViewModel.php`: constructor promotion with exactly `public string $title` and `public \App\Models\Article $article`; no methods
- [x] Run `docker compose run --rm php composer dump-autoload`
- [x] Create `app/src/Views/articles/show.php` with `/** @var \App\ViewModels\ArticleViewModel $model */`; requires `partials/header.php`, then `partials/article.php` inside `<main>` after `$article = $model->article;`, then `partials/footer.php`
- [x] `partials/header.php`: change only the DocBlock to `ArticlesViewModel|ArticleViewModel`
- [x] Do not change `partials/article.php`; no CSS, `style` or `class` attributes

## Controller

- [x] Add `use App\Models\Article;` and `use App\ViewModels\ArticleViewModel;`; `index` and the constructor unchanged
- [x] `show`: `get((int) $vars['id'])`; if `null`, send 404 + `Not Found` and return; otherwise `$model = new ArticleViewModel($article->title, $article)` and `require` `Views/articles/show.php`
- [x] `store`: new `Article` from `$_POST['title'|'content'|'author'] ?? ''`, `create`, then `header('Location: /articles', true, 302)`
- [x] `update`: `get` the article (404 + `Not Found` if missing), overwrite `title`/`content`/`author` from `$_POST` (`?? ''`), `update`, redirect 302 to `/articles`
- [x] `destroy`: 404 + `Not Found` if `get` returns `null`, otherwise `delete` and redirect 302 to `/articles`
- [x] No validation, no render helper or base controller

## Routes

- [x] `app/public/index.php`: add `GET /articles/{id:\d+}` → `show`, `POST /articles/create` → `store`, `POST /articles/{id:\d+}/edit` → `update`, `POST /articles/{id:\d+}/delete` → `destroy`
- [x] Keep `GET /articles` → `index`; add no GET routes for create, edit or delete

## Docs

- [x] `AGENTS.md` Project structure: `app/src/ViewModels/` line lists `ArticlesViewModel`, `ArticleViewModel`; no other edits

## Verify

- [x] Do not edit or delete seed articles (ids 1–20); do not reseed or truncate. Use one throwaway row for the write checks
- [x] `GET /articles/1` → 200; `<title>` and `<h1>` are article 1's title; the page shows title, author, `created_at`, content
- [x] `GET /articles/9999` → 404 `Not Found`
- [x] `POST /articles/create` (title, content, author) → 302 `Location: /articles`; new row has DB-set `created_at` and appears on `/articles`
- [x] `POST /articles/<newId>/edit` → 302 to `/articles`; title/content/author updated, `created_at` unchanged
- [x] `POST /articles/<newId>/delete` → 302 to `/articles`; row gone, `GET /articles/<newId>` → 404
- [x] `POST /articles/9999/edit` and `POST /articles/9999/delete` → 404; still 20 rows
- [x] `GET /articles/create`, `GET /articles/1/edit`, `GET /articles/1/delete` → 405
- [x] `GET /articles` behaves as before (20 seed articles)
- [x] `git diff` shows `IArticleService.php` and `IArticleRepository.php` unchanged
- [x] No PHP warnings or notices in responses
