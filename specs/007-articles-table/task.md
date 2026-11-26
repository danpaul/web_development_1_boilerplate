# Tasks: Articles table

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Migration

- [x] Start `mysql` if needed (`docker compose up -d mysql`); `docker compose run --rm php vendor/bin/phinx status` connects without errors
- [x] Run `docker compose run --rm php vendor/bin/phinx create CreateArticlesTable` (never create the file by hand)
- [x] In the generated file's `change()`, create table `articles` with the implicit `id` (unsigned, do not override)
- [x] Add columns: `title` (`string`, limit 255), `content` (`text`), `author` (`string`, limit 255), `created_at` (`datetime`, default `CURRENT_TIMESTAMP`)
- [x] Add indexes with Phinx default names: `addIndex(['title'])`, `addIndex(['author'])`, `addIndex(['created_at'])`, `addIndex(['content'], ['type' => 'fulltext'])`
- [x] Do not add `null` options, `addTimestamps()`, `updated_at`, or `up()`/`down()`
- [x] Run `docker compose run --rm php vendor/bin/phinx migrate`

## Verify

- [x] `phinx status` shows `CreateArticlesTable` as `up`
- [x] `DESCRIBE articles` shows exactly `id`, `title`, `content`, `author`, `created_at`; all `Null` = `NO`; `id` is `int(10) unsigned` auto_increment PRI; `created_at` default `current_timestamp()`
- [x] `SHOW INDEX FROM articles` shows `PRIMARY` on `id`, indexes on `title`, `author`, `created_at`, and a `FULLTEXT` index on `content`
- [x] `phinx rollback` (pre-approved, no need to ask) drops `articles`: `SHOW TABLES LIKE 'articles'` returns nothing
- [x] `phinx migrate` again recreates `articles` with the same columns and indexes; status shows `up`
- [x] `git status` shows only the new migration file (plus the spec folder) as changed
