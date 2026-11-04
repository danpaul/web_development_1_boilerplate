## Goal

Add Phinx so database schema changes are made through migrations, and document how to create and run migrations in README.md and the agent conventions for database changes in AGENTS.md.

## Requirements

- Add `robmorgan/phinx` to `require` in `app/composer.json` and update `app/composer.lock`.
- Add `app/phinx.php` (PHP config, not YAML) with:
  - a single `development` environment, set as the default
  - migrations path `app/db/migrations` (`%%PHINX_CONFIG_DIR%%/db/migrations`) and seeds path `app/db/seeds`
  - connection settings read from the env vars `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASSWORD` via `getenv()`, falling back to the dev values `mysql`, `developmentdb`, `developer` and `secret123`
- Add those env vars with the dev values to the `php` service in `docker-compose.yml`.
- Phinx is run directly inside the php container: `docker compose run --rm php vendor/bin/phinx <command>`. No Composer script aliases.
- README.md gets a migrations section that explains:
  - creating a migration (`create`), applying it (`migrate`), undoing it (`rollback`) and checking it (`status`)
  - the structure of a migration file: the timestamped filename, the class, `change()` vs `up()`/`down()`
  - table and column fields: the implicit `id` column, common column types (`string`, `integer`, `text`, `boolean`, `datetime`, `decimal`), common column options (`limit`, `null`, `default`, `signed`), `addTimestamps()`, indexes and foreign keys
- AGENTS.md is updated to:
  - list `app/phinx.php` and `app/db/migrations/` under project structure and `robmorgan/phinx` under dependencies
  - add the Phinx commands to the command list
  - add these database-change conventions:
    - every schema change goes through a Phinx migration, never phpMyAdmin or hand-run SQL
    - create migration files with `vendor/bin/phinx create CamelCaseName`, never by hand
    - each migration makes one logical change
    - ask the user before any destructive change (dropping tables or columns, or anything else that loses data)
- Verify the setup with a temporary migration that creates a test table with a few columns: run `migrate`, confirm the table exists, run `rollback`, confirm it is gone, then delete the migration file.

## Acceptance Criteria

- `docker compose run --rm php vendor/bin/phinx status` connects to the database without errors.
- A test table was created by `migrate` and removed by `rollback`.
- No migration files are committed, and the test table does not exist in `developmentdb`.
- README.md covers `create`, `migrate`, `rollback` and `status`, and explains the migration file structure, column types and options listed above.
- AGENTS.md contains the four database-change conventions listed above, and its structure, dependency and command lists include Phinx.

## Out of Scope

- creating any specific database tables or any code integrating with the data layer
