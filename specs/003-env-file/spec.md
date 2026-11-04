## Goal

Move the local database credentials into a gitignored `.env` file at the project root, read by Docker Compose, so the php, mysql and phpMyAdmin services share a single source of truth. A tracked `.env.example` provides the default local credentials, and README.md and AGENTS.md document the setup.

## Requirements

- Add a tracked `.env.example` at the project root with the default local credentials:
  - `DB_HOST=mysql`
  - `DB_NAME=developmentdb`
  - `DB_USER=developer`
  - `DB_PASSWORD=secret123`
  - `DB_ROOT_PASSWORD=secret123`
- `.env` lives at the project root and is gitignored. `.gitignore` already ignores `.env` and keeps `.env.example` tracked; keep it that way.
- Students create `.env` by hand with `cp .env.example .env` before `docker compose up`. It is not created automatically.
- Environment values reach PHP through Docker Compose, not a PHP library. Don't add `vlucas/phpdotenv` or any other new dependency.
- In `docker-compose.yml`, no DB credentials are hardcoded. Every value comes from `.env` by `${VAR}` substitution:
  - `php` service: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`
  - `mysql` service: `MARIADB_DATABASE` ← `DB_NAME`, `MARIADB_USER` ← `DB_USER`, `MARIADB_PASSWORD` ← `DB_PASSWORD`, `MARIADB_ROOT_PASSWORD` ← `DB_ROOT_PASSWORD`
  - `phpmyadmin` service: `PMA_HOST` ← `DB_HOST`
- Each substitution uses the required form (`${VAR:?message}`). If `.env` is missing, `docker compose up` stops with a message telling the student to copy `.env.example` to `.env`.
- `app/phinx.php` keeps reading `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASSWORD` via `getenv()`, but without the hardcoded fallback values.
- README.md documents:
  - the `cp .env.example .env` setup step, before `docker compose up`
  - what each variable in `.env.example` is for
  - that `.env` is gitignored and must never be committed
  - that MariaDB only applies credentials when the `mysqldata` volume is first created. Changing them later requires `docker compose down -v`, which deletes all database data.
- AGENTS.md is updated to:
  - list `.env.example` (and the gitignored `.env`) under project structure
  - add `cp .env.example .env` to the command list
  - say that the dev credentials come from `.env` and that the defaults are in `.env.example`
  - add a convention: never commit `.env`. New DB-related variables go in `.env.example` and in the `${VAR:?}` substitutions in `docker-compose.yml`.

## Acceptance Criteria

- `.env.example` exists at the project root with the five variables above, and `.env` is ignored by git (`git check-ignore .env` matches).
- `docker-compose.yml` contains no literal DB credentials, hosts or database names for the php, mysql or phpmyadmin services.
- With `.env` copied from `.env.example`, `docker compose up` starts all services, and `docker compose run --rm php vendor/bin/phinx status` connects to the database without errors.
- Without `.env`, `docker compose up` fails with the message to copy `.env.example`.
- `app/phinx.php` has no hardcoded credential fallbacks.
- README.md and AGENTS.md contain the documentation listed above.

## Out of Scope

- any additional .env configuration (beyond database)
- PHP code for an application database connection (e.g. a PDO class or a route that uses it)
