# Plan: .env file

Spec: [./spec.md](./spec.md)

Move the DB credentials out of `docker-compose.yml` into a gitignored root `.env` (template: tracked `.env.example`), substituted by Docker Compose with required `${VAR:?}` syntax, drop the fallbacks in `app/phinx.php`, and document the setup in `README.md` and `AGENTS.md`.

## Technical context

- Docker Compose automatically reads `.env` from the project root (next to `docker-compose.yml`) for `${VAR}` interpolation. No `env_file:` is needed.
- Variables already set in the shell take precedence over `.env`.
- `docker-compose.yml` currently hardcodes:
  - `php`: `DB_HOST: 'mysql'`, `DB_NAME: 'developmentdb'`, `DB_USER: 'developer'`, `DB_PASSWORD: 'secret123'` (map style)
  - `mysql`: `MARIADB_ROOT_PASSWORD`, `MARIADB_USER`, `MARIADB_PASSWORD`, `MARIADB_DATABASE` (map style)
  - `phpmyadmin`: `- PMA_HOST=mysql` (list style)
- `app/phinx.php` reads `getenv('DB_*') ?: '<default>'`. Port `3306` and charset are fixed.
- `.gitignore` already has `.env`, `.env.*`, `!.env.example`, `!/app/.env.example`. No change needed.
- MariaDB applies `MARIADB_*` only when the `mysqldata` volume is first initialised.
- `README.md`: `## Setup` (numbered list, 2 steps), `## Usage` (`### Composer Autoload`, `### NGINX`, `### PHPMyAdmin`, `### Stopping…`), `## Database migrations`. The `### PHPMyAdmin` section says "Credentials are defined in `docker-compose.yml`".
- No new dependencies (no `vlucas/phpdotenv`).

## Target structure

- Add `.env.example`
- Change `docker-compose.yml`
- Change `app/phinx.php`
- Change `README.md`
- Change `AGENTS.md`
- Local only, not committed: `.env` (created with `cp .env.example .env` for verification)

## .env.example

Exactly these five lines, no comments:

```
DB_HOST=mysql
DB_NAME=developmentdb
DB_USER=developer
DB_PASSWORD=secret123
DB_ROOT_PASSWORD=secret123
```

## docker-compose.yml

Every substitution uses the same message: `Missing .env file - run: cp .env.example .env`. Compose puts the variable name in front of it in the error. Use map style for all three services (convert `phpmyadmin` from list style). Leave all other keys unchanged.

```yaml
    php:
        ...
        environment:
            DB_HOST: ${DB_HOST:?Missing .env file - run: cp .env.example .env}
            DB_NAME: ${DB_NAME:?Missing .env file - run: cp .env.example .env}
            DB_USER: ${DB_USER:?Missing .env file - run: cp .env.example .env}
            DB_PASSWORD: ${DB_PASSWORD:?Missing .env file - run: cp .env.example .env}
    mysql:
        ...
        environment:
            MARIADB_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:?Missing .env file - run: cp .env.example .env}
            MARIADB_USER: ${DB_USER:?Missing .env file - run: cp .env.example .env}
            MARIADB_PASSWORD: ${DB_PASSWORD:?Missing .env file - run: cp .env.example .env}
            MARIADB_DATABASE: ${DB_NAME:?Missing .env file - run: cp .env.example .env}
    phpmyadmin:
        ...
        environment:
            PMA_HOST: ${DB_HOST:?Missing .env file - run: cp .env.example .env}
```

## app/phinx.php

Remove only the fallbacks, using plain `getenv()`. There is no helper and no exception. Port and charset stay as they are.

```php
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASSWORD'),
```

## README.md

- `## Setup`: add step 3: copy the env file, `cp .env.example .env` (bash code block), before running `docker compose up`.
- New `## Environment variables` section directly after `## Setup` (before `## Usage`):
  - One line: Docker Compose reads `.env` and passes the values to the php, mysql and phpMyAdmin services.
  - Table of the variables with what each is for:
    - `DB_HOST`: database host as seen from the containers (the `mysql` service). Used by PHP/Phinx and phpMyAdmin.
    - `DB_NAME`: database created by MariaDB and used by the app.
    - `DB_USER` / `DB_PASSWORD`: the app's database user, also used for the phpMyAdmin login.
    - `DB_ROOT_PASSWORD`: MariaDB root password.
  - `.env` is gitignored and must never be committed. `.env.example` is the tracked template.
  - Without `.env`, `docker compose up` stops with a message telling you to copy `.env.example`.
  - MariaDB applies the credentials only when the `mysqldata` volume is first created. To change them later, run `docker compose down -v` (bash block), which **deletes all database data**, then `docker compose up`.
- `### PHPMyAdmin`: replace "Credentials are defined in `docker-compose.yml`. They are: developer/secret123" with: log in with `DB_USER` / `DB_PASSWORD` from `.env` (default `developer` / `secret123`).

## AGENTS.md

- **Project structure**: after the `nginx.conf` line, add:
  - `` `.env.example`: tracked default DB credentials. Copy to `.env` (gitignored), which Docker Compose reads ``
- **Commands**: add `cp .env.example .env` as the first line of the code block.
- **Services and credentials**: replace the credentials bullet with: the credentials come from `.env`. The defaults in `.env.example` are database `developmentdb`, user `developer` / `secret123`, root password `secret123`. Keep the other bullets.
- New `## Environment` section after **Services and credentials** (before **Database changes**), with these bullets:
  - Never commit `.env`.
  - New DB-related variables go in `.env.example` and in a `${VAR:?Missing .env file - run: cp .env.example .env}` substitution in `docker-compose.yml`.

## Implementation order

1. Add `.env.example`, then `cp .env.example .env`
2. Update `docker-compose.yml`
3. Update `app/phinx.php`
4. Run the checks with `.env` present and without it (see `task.md`)
5. Update `README.md`
6. Update `AGENTS.md`
7. Verify (see `task.md`)

## Out of scope

- Non-database `.env` variables (app env, ports, debug flags), including making the phinx port or charset configurable
- A PHP DB connection class, routes that use the database, or any dotenv library
- Auto-creating `.env` (scripts, entrypoint, Composer hooks)
- Changing `.gitignore`, `PHP.Dockerfile`, `nginx.conf`, or service images and ports
- Fixing unrelated parts of `README.md` (for example the stale `hello.php` references)
