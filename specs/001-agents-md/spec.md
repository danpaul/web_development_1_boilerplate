## Goal

Add an AGENTS.md file at the repo root that gives AI coding agents accurate context about this repo as it is now. The repo is a class learning repo and the starting point for student projects. Also add a CLAUDE.md that imports it so Claude Code loads the same context.

## Requirements

- Create `AGENTS.md` at the repo root.
- Create `CLAUDE.md` at the repo root containing only the line `@AGENTS.md`.
- AGENTS.md describes the project as a PHP Docker boilerplate used for class learning and as a starting point for student projects.
- AGENTS.md describes the current project structure:
  - `docker-compose.yml`, `PHP.Dockerfile`, `nginx.conf`
  - `app/composer.json` and `app/composer.lock`; `app/vendor/` is generated and gitignored, and the php container runs `composer install` on start if it is missing
  - `app/public/index.php` (front controller)
  - `app/src/Controllers/` (`HomeController`, `HelloController`)
  - `specs/`, `.agents/skills/`, `.claude/skills/`, `skills-lock.json`
- AGENTS.md lists dependencies by name only, without versions, and points to `docker-compose.yml`, `PHP.Dockerfile` and `app/composer.json` as the source of truth:
  - services: nginx, php-fpm (with the PDO and pdo_mysql extensions, and Composer), MariaDB, phpMyAdmin
  - Composer package: nikic/fast-route
- AGENTS.md explains the request flow: nginx serves `app/public`, sends requests that don't match a file to `index.php`, FastRoute maps them to `App\Controllers\*` methods, and each method receives the route variables as `$vars`. It also states that the PSR-4 namespace `App\` maps to `app/src/`.
- AGENTS.md lists these commands: `docker compose up`, `docker compose down`, `docker compose run --rm php composer install` and `docker compose run --rm php composer dump-autoload`.
- AGENTS.md lists the service URLs and development credentials from `docker-compose.yml`: the app at `http://localhost`, phpMyAdmin at `http://localhost:8080`, MariaDB on port 3306 (database `developmentdb`, user `developer`/`secret123`, root password `secret123`).
- AGENTS.md describes the spec workflow: specs live in `specs/<id>/spec.md`, the `spec-init`, `spec-grill-me`, `spec-plan` and `spec-execute` skills are in `.agents/skills/` and mirrored in `.claude/skills/`, and `skills-lock.json` records where each skill comes from.
- AGENTS.md does not mention `app/public/contact.php` or `app/public/hello.php`.

## Acceptance Criteria

- `AGENTS.md` exists at the repo root and covers the project purpose, structure, dependencies, request flow, commands, URLs/credentials and spec workflow described above.
- Every file, folder, command, URL and credential in AGENTS.md matches the current repo.
- AGENTS.md lists no dependency versions.
- AGENTS.md doesn't contain the strings `contact.php` or `hello.php`.
- `CLAUDE.md` exists at the repo root and contains only `@AGENTS.md`.

## Out of Scope

- Establishing additional code conventions, or tailoring this repo to a specific project.
