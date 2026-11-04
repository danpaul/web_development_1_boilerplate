# Tasks: .env file

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Env file

- [x] Add `.env.example` at the project root with exactly `DB_HOST=mysql`, `DB_NAME=developmentdb`, `DB_USER=developer`, `DB_PASSWORD=secret123`, `DB_ROOT_PASSWORD=secret123`
- [x] Run `cp .env.example .env` locally. Do not commit `.env`.
- [x] Do not change `.gitignore`. Do not add `vlucas/phpdotenv` or any other dependency. Do not auto-create `.env`.

## docker-compose.yml

- [x] `php`: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` all from `${VAR:?Missing .env file - run: cp .env.example .env}`
- [x] `mysql`: `MARIADB_DATABASE` ← `DB_NAME`, `MARIADB_USER` ← `DB_USER`, `MARIADB_PASSWORD` ← `DB_PASSWORD`, `MARIADB_ROOT_PASSWORD` ← `DB_ROOT_PASSWORD`, same `:?` message
- [x] `phpmyadmin`: `PMA_HOST` ← `DB_HOST`, same `:?` message. Convert to map style.
- [x] No literal credentials, hosts or database names remain in these three services. Other keys are unchanged.

## app/phinx.php

- [x] Use plain `getenv('DB_HOST')`, `getenv('DB_NAME')`, `getenv('DB_USER')`, `getenv('DB_PASSWORD')` with no `?:` fallbacks
- [x] Keep port `3306`, charset and the rest of the config unchanged

## README.md

- [x] `## Setup`: add step 3 `cp .env.example .env` (before `docker compose up`)
- [x] Add `## Environment variables` after `## Setup` with: Compose reads `.env`; a table describing `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`; `.env` is gitignored and must never be committed; a missing `.env` makes `docker compose up` stop with the copy message
- [x] Volume caveat: credentials apply only when the `mysqldata` volume is first created. Changing them needs `docker compose down -v`, which deletes all database data.
- [x] `### PHPMyAdmin`: replace "Credentials are defined in `docker-compose.yml`…" with "log in with `DB_USER` / `DB_PASSWORD` from `.env`" (default `developer` / `secret123`)
- [x] Do not edit other README sections

## AGENTS.md

- [x] Project structure: add `.env.example` (tracked defaults, copy to gitignored `.env` that Compose reads)
- [x] Commands: add `cp .env.example .env` as the first line
- [x] Services and credentials: say the credentials come from `.env` and that the defaults are in `.env.example` (`developmentdb`, `developer` / `secret123`, root `secret123`)
- [x] Add `## Environment` after Services and credentials: never commit `.env`; new DB-related variables go in `.env.example` and in a `${VAR:?…}` substitution in `docker-compose.yml`

## Verify

- [x] `git check-ignore .env` prints `.env`. `git status` does not list `.env` but does list `.env.example`.
- [x] `grep -nE "secret123|developmentdb|developer|'mysql'|=mysql" docker-compose.yml` finds no matches in `environment:` blocks
- [x] `grep -n "?:" app/phinx.php` finds nothing
- [x] With `.env` present: `docker compose up -d` starts nginx, php, mysql and phpmyadmin, and `docker compose run --rm php vendor/bin/phinx status` connects without errors
- [x] With `.env` renamed away (`mv .env .env.bak`): `docker compose up` fails with `Missing .env file - run: cp .env.example .env`. Then restore it with `mv .env.bak .env`. Make sure no `DB_*` variables are set in the shell.
- [x] phpMyAdmin at `http://localhost:8080` logs in with `developer` / `secret123`
- [x] README.md and AGENTS.md contain everything listed above
- [x] Only the files listed in the plan's Target structure changed (`.env` is not tracked)
