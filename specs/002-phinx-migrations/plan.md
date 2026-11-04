# Plan: Phinx migrations

Spec: [./spec.md](./spec.md)

Add Phinx with a PHP config and DB env vars, verify it with a throwaway migration, and document migrations in `README.md` and the database-change conventions in `AGENTS.md`.

## Technical context

- `php` service in `docker-compose.yml` is built from `PHP.Dockerfile` (`WORKDIR /app`, `./app` mounted at `/app`) and has no `environment:` block yet.
- `mysql` service: MariaDB, `MARIADB_DATABASE=developmentdb`, `MARIADB_USER=developer`, `MARIADB_PASSWORD=secret123`. Reachable from containers as host `mysql`, port `3306`.
- `app/composer.json` requires only `nikic/fast-route`; `app/composer.lock` is tracked; `app/vendor/` is gitignored.
- Commands run in the container from `/app`, so `vendor/bin/phinx` resolves to `app/vendor/bin/phinx` and Phinx finds `phinx.php` in the working directory.
- `app/db/` does not exist yet.
- `README.md` is a single `#` title with `##` sections (`Setup`, `Usage`) and `###` subsections. It still mentions `hello.php`; leave that as is.
- `AGENTS.md` sections: Project structure, Dependencies, Request flow, Commands, Services and credentials, Spec workflow.

## Target structure

- Change `app/composer.json`, `app/composer.lock` (add `robmorgan/phinx`)
- Add `app/phinx.php`
- Add `app/db/migrations/.gitkeep`, `app/db/seeds/.gitkeep`
- Change `docker-compose.yml` (`php` service `environment:`)
- Change `README.md` (new `## Database migrations` section at the end)
- Change `AGENTS.md`
- Temporary only (created, then deleted): one migration in `app/db/migrations/`

## Composer

- Run `docker compose run --rm php composer require robmorgan/phinx`. Keep the version constraint Composer writes. This updates `composer.json` and `composer.lock` together.

## app/phinx.php

```php
<?php

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'mysql',
            'name' => getenv('DB_NAME') ?: 'developmentdb',
            'user' => getenv('DB_USER') ?: 'developer',
            'pass' => getenv('DB_PASSWORD') ?: 'secret123',
            'port' => 3306,
            'charset' => 'utf8mb4',
        ],
    ],
    'version_order' => 'creation',
];
```

- Only the `development` environment. Port and charset are fixed; no extra env vars.

## docker-compose.yml

Add to the `php` service, in the same map style as `mysql`:

```yaml
        environment:
            DB_HOST: 'mysql'
            DB_NAME: 'developmentdb'
            DB_USER: 'developer'
            DB_PASSWORD: 'secret123'
```

## README.md

Append `## Database migrations` at the end of the file, with `###` subsections:

1. Intro: schema changes are made with [Phinx](https://phinx.org) migrations; config is `app/phinx.php`; migrations live in `app/db/migrations/`; Phinx runs inside the php container.
2. **Commands** — one code block each, with a one-line explanation:
   - `docker compose run --rm php vendor/bin/phinx create CreateUsersTable` (name must be CamelCase; creates the file)
   - `docker compose run --rm php vendor/bin/phinx migrate` (apply all pending migrations)
   - `docker compose run --rm php vendor/bin/phinx rollback` (undo the last migration)
   - `docker compose run --rm php vendor/bin/phinx status` (list migrations and whether each is up or down)
3. **Migration file structure**:
   - Filename: `YYYYMMDDHHMMSS_snake_case_name.php`, e.g. `20261104120000_create_users_table.php`; the timestamp sets the run order. Don't rename files after they've been run.
   - Class: CamelCase name matching the filename, extends `Phinx\Migration\AbstractMigration`.
   - `change()`: write the forward change only; Phinx reverses it on rollback (works for create/add/rename, etc.).
   - `up()`/`down()`: use when the change can't be reversed automatically (e.g. raw SQL, data changes); `down()` must undo `up()`.
   - One full example migration using `change()`, e.g. `CreatePostsTable`:
     - `$this->table('posts')` with columns `title` (`string`, `limit` 150), `body` (`text`, `null` true), `views` (`integer`, `signed` false, `default` 0), `published` (`boolean`, `default` false), `published_at` (`datetime`, `null` true), `price` (`decimal`, `precision`/`scale`), `user_id` (`integer`, `signed` false)
     - `->addTimestamps()`, `->addIndex(['title'])` (show `['unique' => true]` option in a comment or text), `->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE'])`, `->create()`
   - A short `up()`/`down()` sketch (e.g. `up()` runs `$this->execute(...)`, `down()` reverses it).
4. **Columns and options**:
   - Implicit `id`: every table gets an auto-increment unsigned integer primary key `id` unless `['id' => false]` is passed.
   - Common types: `string`, `integer`, `text`, `boolean`, `datetime`, `decimal` — one line each on what to use it for.
   - Common options: `limit`, `null`, `default`, `signed` — one line each (note columns are `NOT NULL` by default; `signed => false` for ids/counters; `decimal` uses `precision` and `scale`).
   - `addTimestamps()`: adds `created_at` and `updated_at`.
   - Indexes: `addIndex(['col'])`, `['unique' => true]`.
   - Foreign keys: `addForeignKey('user_id', 'users', 'id', [...])`; the column must match the referenced `id` type (unsigned integer); `delete`/`update` options (`CASCADE`, `SET_NULL`, `RESTRICT`, `NO_ACTION`).
   - Changing an existing table: `$this->table('posts')->addColumn(...)->update()`.

The example table names are documentation only; no `posts`/`users` migrations are created.

## AGENTS.md

- **Project structure**: add after the `app/composer.json` line:
  - `app/phinx.php`: Phinx config (env vars `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`)
  - `app/db/migrations/`: Phinx migrations (`app/db/seeds/` for seeds)
- **Dependencies**: change the Composer line to `Composer packages: nikic/fast-route, robmorgan/phinx` (names only, no versions).
- **Commands**: add to the code block:
  ```sh
  docker compose run --rm php vendor/bin/phinx create CamelCaseName
  docker compose run --rm php vendor/bin/phinx migrate
  docker compose run --rm php vendor/bin/phinx rollback
  docker compose run --rm php vendor/bin/phinx status
  ```
- New section `## Database changes`, placed after **Services and credentials**, with exactly these four bullets:
  - Every schema change goes through a Phinx migration, never phpMyAdmin or hand-run SQL.
  - Create migration files with `vendor/bin/phinx create CamelCaseName`, never by hand.
  - Each migration makes one logical change.
  - Ask the user before any destructive change (dropping tables or columns, or anything else that loses data).

## Verification migration

- `docker compose up -d mysql` (if not running), then `docker compose run --rm php vendor/bin/phinx status` — must connect without errors.
- `docker compose run --rm php vendor/bin/phinx create CreatePhinxTestTable`.
- Fill `change()` with `$this->table('phinx_test')->addColumn('name', 'string', ['limit' => 100])->addColumn('quantity', 'integer', ['default' => 0])->addColumn('active', 'boolean', ['default' => true])->addTimestamps()->create();`
- `migrate`, then confirm the table exists: `docker compose exec mysql mariadb -udeveloper -psecret123 developmentdb -e "SHOW TABLES LIKE 'phinx_test'"`.
- `rollback`, run the same query and confirm it returns nothing.
- Delete the migration file; `status` shows no migrations. The `phinxlog` table stays (it's Phinx's own bookkeeping, not a test table).

## Implementation order

1. `composer require robmorgan/phinx`
2. Add `app/phinx.php`, `app/db/migrations/.gitkeep`, `app/db/seeds/.gitkeep`
3. Add env vars to `docker-compose.yml`
4. Run the verification migration cycle; delete the migration file
5. Write the `README.md` section
6. Update `AGENTS.md`
7. Verify (see `task.md`)

## Out of scope

- Creating any real tables, seeds or data-layer code (no PDO connection class, no models)
- A `testing`/`production` Phinx environment, `.env` files, or Composer script aliases
- Changing `PHP.Dockerfile`, `nginx.conf`, or the `mysql`/`phpmyadmin` services
- Fixing other parts of `README.md` (e.g. the stale `hello.php` references)
