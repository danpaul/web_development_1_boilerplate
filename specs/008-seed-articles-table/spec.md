## Goal

Fill the `articles` table with 20 fixed sample articles using a Phinx seeder, and document how to run it.

## Requirements

- Create the seeder with `docker compose run --rm php vendor/bin/phinx seed:create ArticlesSeeder`, never by hand. It goes in `app/db/seeds/`.
- The seeder inserts exactly 20 hardcoded, readable articles into the `articles` table from the `CreateArticlesTable` migration. Add no new Composer dependencies (no Faker).
- Each row sets `title`, `content` and `author`. All three are non-empty, and `content` is at least one full sentence.
- `created_at` is set explicitly on every row, with dates spread over the past (for example one article per day). Do not rely on the `CURRENT_TIMESTAMP` default.
- The articles share a small set of 3–5 distinct authors, so each author has several articles.
- `id` is left to auto increment.
- The seeder truncates `articles` before inserting. Running it again leaves exactly 20 rows.
- Run the seeder with `docker compose run --rm php vendor/bin/phinx seed:run -s ArticlesSeeder`, using the existing `app/phinx.php` config and `.env` credentials. The `articles` migration must already be applied.
- `README.md`: add a seeding section after the "Database migrations" section. It covers what seeders are and where they live (`app/db/seeds/`), how to create one (`seed:create`), how to run all seeders (`seed:run`) and one seeder (`seed:run -s ArticlesSeeder`), and a warning that `ArticlesSeeder` truncates the table first.
- `AGENTS.md`: list `app/db/seeds/ArticlesSeeder.php` under Project structure. Add the `seed:create` and `seed:run` commands to Commands. Describe the `articles` table and its seed data (20 articles, 3–5 authors, varied `created_at`, truncates on run).

## Acceptance Criteria

- `app/db/seeds/ArticlesSeeder.php` exists and `phinx seed:run -s ArticlesSeeder` finishes without errors
- `SELECT COUNT(*) FROM articles` returns 20
- No row has an empty `title`, `content` or `author`, and every `created_at` is set
- `SELECT COUNT(DISTINCT author) FROM articles` returns a value between 3 and 5
- `created_at` values differ between rows
- Running the seeder a second time still leaves exactly 20 rows
- `README.md` and `AGENTS.md` contain the seeding information listed above

## Out of Scope

- Any other backend code changes (models, services, controllers, routes)
- Schema changes to the `articles` table
