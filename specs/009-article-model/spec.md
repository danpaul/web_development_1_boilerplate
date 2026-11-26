## Goal

Add an `Article` model class that mirrors the `articles` table, so rows can be loaded straight into `Article` objects with PDO.

## Requirements

- Create `App\Models\Article` in `app/src/Models/Article.php`, autoloaded through the existing PSR-4 mapping (`App\` → `app/src/`). Run `composer dump-autoload` after adding it.
- The model's fields match the table defined in `app/db/migrations/20261110131728_create_articles_table.php` exactly: same names, including snake_case (`created_at`), with no extra or missing fields.
- Fields are public typed properties:
  - `public ?int $id = null`
  - `public string $title`
  - `public string $content`
  - `public string $author`
  - `public ?string $created_at = null` (raw DB datetime string, e.g. `2026-10-01 10:00:00`)
- `id` and `created_at` are nullable so `new Article()` is valid for an article that has not been saved yet.
- The class can be loaded directly with `PDO::FETCH_CLASS, Article::class` (for example `$stmt->fetchAll(PDO::FETCH_CLASS, Article::class)`). It needs no constructor arguments, and no constructor overwrites the fetched values.
- The model holds data only: no database access, query methods or other behaviour.
- Update `README.md` and `AGENTS.md` with the model conventions:
  - Models live in `app/src/Models/` under the `App\Models` namespace.
  - A model's property names and types match its table's columns exactly, as defined by the migration.
  - Models can be loaded with `PDO::FETCH_CLASS`.
  - Add `app/src/Models/` (`Article`) to the project structure in `AGENTS.md`.

## Acceptance Criteria

- `app/src/Models/Article.php` exists and defines `App\Models\Article` with exactly the properties `id`, `title`, `content`, `author`, `created_at`.
- With the seeded database, a one-off manual check in the php container (PDO query `SELECT * FROM articles`, then `fetchAll(PDO::FETCH_CLASS, Article::class)`) returns 20 `Article` objects whose properties equal the row values, with no errors or dynamic-property deprecations. This check is not committed.
- `new Article()` works without arguments, and `id` and `created_at` are `null`.
- `README.md` and `AGENTS.md` describe the model conventions above.

## Out of Scope

- any further code changes
