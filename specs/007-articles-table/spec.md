## Goal

Add an `articles` table to the MariaDB database through a Phinx migration.

## Requirements

- Create the migration with `docker compose run --rm php vendor/bin/phinx create CreateArticlesTable`, never by hand; it lands in `app/db/migrations/`
- The migration makes one logical change: create the `articles` table
- Implement it in `change()` so it is reversible (rollback drops the table)
- Columns, all NOT NULL:
  - `id` — int, auto increment, primary key
  - `title` — varchar(255), indexed
  - `content` — text, fulltext index
  - `author` — varchar(255), indexed
  - `created_at` — datetime, default `CURRENT_TIMESTAMP`, indexed
- No other columns (no `updated_at`)
- Run with `docker compose run --rm php vendor/bin/phinx migrate`, using the existing `app/phinx.php` config and `.env` credentials

## Acceptance Criteria

- A `CreateArticlesTable` migration file exists in `app/db/migrations/` and `phinx status` shows it as `up`
- `DESCRIBE articles` shows exactly `id`, `title`, `content`, `author`, `created_at` with the types, NOT NULL constraints and `created_at` default listed above
- `SHOW INDEX FROM articles` shows the primary key on `id`, regular indexes on `title`, `author` and `created_at`, and a FULLTEXT index on `content`
- `phinx rollback` drops the `articles` table, and running `phinx migrate` again recreates it

## Out of Scope

- Seeding data
- Any other backend code (models, controllers, routes)
