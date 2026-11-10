# Tasks: Article model

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Model

- [x] Create `app/src/Models/Article.php` with `namespace App\Models;` and `class Article`
- [x] Add exactly these properties, in this order: `public ?int $id = null`, `public string $title`, `public string $content`, `public string $author`, `public ?string $created_at = null`
- [x] Keep `created_at` snake_case; no extra or missing properties
- [x] No constructor, no methods, no PDO/database code
- [x] Run `docker compose run --rm php composer dump-autoload`

## Docs

- [x] `README.md`: add `### Models` after `### Composer Autoload` (before `### NGINX`)
- [x] README: models live in `app/src/Models/` under `App\Models`; property names and types match the table's columns exactly, as defined by the migration; auto-filled columns nullable with `null` default; data only
- [x] README: short `fetchAll(PDO::FETCH_CLASS, Article::class)` example
- [x] `AGENTS.md` Project structure: add `` `app/src/Models/`: `Article` `` after the `app/src/Controllers/` line
- [x] `AGENTS.md`: add `## Models` after `## Database tables` with the same conventions (location/namespace, properties match migration columns exactly, data only, loadable with `PDO::FETCH_CLASS`, no constructor arguments)

## Verify

- [x] DB migrated and seeded (`phinx status` shows `CreateArticlesTable` up; run `phinx seed:run -s ArticlesSeeder` if needed)
- [x] Run the manual check from the plan via `docker compose run --rm -T php php <<'PHP' ... PHP` (stdin, no file created)
- [x] Check prints `20` Article objects, each `get_object_vars($article) === $row` from `FETCH_ASSOC`
- [x] `new Article()` works with no arguments; `id` and `created_at` are `null`
- [x] No warnings, errors or `Creation of dynamic property` deprecations in the output
- [x] Do not commit the check script or any test file
- [x] `git status` shows only `app/src/Models/Article.php`, `README.md`, `AGENTS.md` (plus the spec folder) as changed
