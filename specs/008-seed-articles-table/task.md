# Tasks: Seed articles table

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Seeder

- [x] Start `mysql` if needed (`docker compose up -d mysql`); `docker compose run --rm php vendor/bin/phinx status` shows `CreateArticlesTable` as `up`
- [x] Run `docker compose run --rm php vendor/bin/phinx seed:create ArticlesSeeder` (never create the file by hand); file lands in `app/db/seeds/`
- [x] Keep the generated class, `declare(strict_types=1)` and docblock; edit only `run()`
- [x] In `run()`, define 20 literal rows with `title`, `content`, `author`, `created_at` (no `id`, no loops, no computed dates)
- [x] Topics: web development, one distinct topic and unique title per article
- [x] `content`: 2–3 full sentences, plain text
- [x] Authors: `Alice Jansen` ×8, `Bram de Vries` ×7, `Chloe Peters` ×5, interleaved across dates
- [x] `created_at`: `2026-10-01 09:00:00` through `2026-10-20 09:00:00`, one per day, rows in ascending date order
- [x] Call `$this->table('articles')->truncate()` before `insert($data)->saveData()`
- [x] Do not add Faker or any other Composer dependency; do not add `getDependencies()`

## Docs

- [x] `README.md`: append `## Database seeding` after the "Database migrations" section (end of file)
- [x] README: explain what seeders are and that they live in `app/db/seeds/` (extend `AbstractSeed`, logic in `run()`)
- [x] README: `### Commands` with `seed:create ArticlesSeeder`, `seed:run` (all), `seed:run -s ArticlesSeeder` (one); note migrations must be run first
- [x] README: `> **Warning:**` that `ArticlesSeeder` truncates `articles` (deletes all rows) before inserting 20 sample articles
- [x] `AGENTS.md` Project structure: replace the `(app/db/seeds/ for seeds)` aside with separate `app/db/seeds/` and `app/db/seeds/ArticlesSeeder.php` entries
- [x] `AGENTS.md` Commands: add `seed:create CamelCaseName`, `seed:run`, `seed:run -s ArticlesSeeder` after `phinx status`
- [x] `AGENTS.md`: add `## Database tables` after "Database changes" describing `articles` columns and its seed data (20 articles, 3 authors, distinct `created_at` 2026-10-01 to 2026-10-20, truncates on every run)

## Verify

- [x] `docker compose run --rm php vendor/bin/phinx seed:run -s ArticlesSeeder` finishes without errors
- [x] `SELECT COUNT(*) FROM articles` → 20
- [x] `SELECT COUNT(*) FROM articles WHERE title = '' OR content = '' OR author = '' OR created_at IS NULL` → 0
- [x] `SELECT COUNT(DISTINCT author) FROM articles` → 3; per-author counts 8 / 7 / 5
- [x] `SELECT COUNT(DISTINCT created_at) FROM articles` → 20; min/max are `2026-10-01 09:00:00` / `2026-10-20 09:00:00`
- [x] Run the seeder a second time → `COUNT(*)` still 20
- [x] `git status` shows only `app/db/seeds/ArticlesSeeder.php`, `README.md`, `AGENTS.md` (plus spec folders / pre-existing untracked files) as changed
