# Tasks: Phinx migrations

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Phinx setup

- [x] Run `docker compose run --rm php composer require robmorgan/phinx` (updates `app/composer.json` and `app/composer.lock`; keep Composer's constraint)
- [x] Add `app/phinx.php` as in the plan: PHP array config (not YAML), only a `development` environment set as default, `mysql` adapter, port `3306`, `utf8mb4`
- [x] Paths: migrations `%%PHINX_CONFIG_DIR%%/db/migrations`, seeds `%%PHINX_CONFIG_DIR%%/db/seeds`
- [x] Connection from `getenv('DB_HOST')`, `getenv('DB_NAME')`, `getenv('DB_USER')`, `getenv('DB_PASSWORD')` with fallbacks `mysql`, `developmentdb`, `developer`, `secret123`
- [x] Add `app/db/migrations/.gitkeep` and `app/db/seeds/.gitkeep`
- [x] Add `environment:` to the `php` service in `docker-compose.yml` with `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` (dev values, map style like `mysql`)
- [x] Do not add Composer script aliases, other Phinx environments or `.env` files

## Verification migration

- [x] With `mysql` running, `docker compose run --rm php vendor/bin/phinx status` connects without errors
- [x] `docker compose run --rm php vendor/bin/phinx create CreatePhinxTestTable`
- [x] In its `change()`, create table `phinx_test` with `name` (string, limit 100), `quantity` (integer, default 0), `active` (boolean, default true) and `addTimestamps()`
- [x] Run `migrate`; confirm `phinx_test` exists (`docker compose exec mysql mariadb -udeveloper -psecret123 developmentdb -e "SHOW TABLES LIKE 'phinx_test'"`)
- [x] Run `rollback`; confirm the same query returns nothing
- [x] Delete the migration file; only `.gitkeep` remains in `app/db/migrations/`

## README.md

- [x] Append `## Database migrations` at the end of the file (intro: Phinx, `app/phinx.php`, `app/db/migrations/`, runs in the php container)
- [x] Commands with one-line explanations: `create` (CamelCase name), `migrate`, `rollback`, `status`, each as `docker compose run --rm php vendor/bin/phinx <command>`
- [x] Migration file structure: timestamped filename and run order, class name / `AbstractMigration`, `change()` vs `up()`/`down()`
- [x] One full `change()` example (`posts` table) using `string`, `integer`, `text`, `boolean`, `datetime`, `decimal`, options `limit`, `null`, `default`, `signed`, plus `addTimestamps()`, `addIndex()` and `addForeignKey()`
- [x] A short `up()`/`down()` sketch
- [x] Columns and options: implicit `id` (and `['id' => false]`), each listed type, each listed option, `addTimestamps()`, indexes (incl. unique), foreign keys (matching unsigned integer, `delete`/`update` options), altering a table with `->update()`
- [x] Do not create `posts`/`users` migrations; do not edit other README sections

## AGENTS.md

- [x] Project structure: add `app/phinx.php` and `app/db/migrations/` (mention `app/db/seeds/`)
- [x] Dependencies: Composer packages `nikic/fast-route`, `robmorgan/phinx` (no versions)
- [x] Commands: add `phinx create CamelCaseName`, `migrate`, `rollback`, `status` (full `docker compose run --rm php vendor/bin/phinx ...` form)
- [x] Add `## Database changes` after Services and credentials with the four conventions: migrations only (never phpMyAdmin or hand-run SQL); create files with `vendor/bin/phinx create CamelCaseName`, never by hand; one logical change per migration; ask the user before any destructive change

## Verify

- [x] `docker compose run --rm php vendor/bin/phinx status` runs without errors and lists no migrations
- [x] `phinx_test` does not exist in `developmentdb`
- [x] `git status` shows no files in `app/db/migrations/` other than `.gitkeep`
- [x] README.md covers `create`, `migrate`, `rollback`, `status`, file structure, all listed column types and options
- [x] AGENTS.md has the four conventions and Phinx in structure, dependencies and commands
- [x] Only the files listed in the plan's Target structure changed
