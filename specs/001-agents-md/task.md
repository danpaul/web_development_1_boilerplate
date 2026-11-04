# Tasks: AGENTS.md and CLAUDE.md

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## AGENTS.md

- [x] Create `AGENTS.md` at the repo root with a title and a one-line intro (PHP Docker boilerplate for class learning and the starting point for student projects)
- [x] Add **Project structure**: `docker-compose.yml`, `PHP.Dockerfile`, `nginx.conf`, `app/composer.json` + `app/composer.lock`, `app/vendor/` (generated, gitignored, installed on php container start if missing), `app/public/index.php` (front controller), `app/src/Controllers/` (`HomeController`, `HelloController`), `specs/`, `.agents/skills/`, `.claude/skills/`, `skills-lock.json`
- [x] Do not list `README.md` or any other `app/public/` file
- [x] Add **Dependencies** by name only: nginx, php-fpm (PDO, pdo_mysql, Composer), MariaDB, phpMyAdmin, Composer package nikic/fast-route
- [x] Point to `docker-compose.yml`, `PHP.Dockerfile` and `app/composer.json` as the source of truth for versions
- [x] Add **Request flow**: nginx serves `app/public` → non-file requests go to `index.php` → FastRoute → `App\Controllers\*` method called with route variables as `$vars`; 404/405 handling; PSR-4 `App\` → `app/src/`; run `dump-autoload` after adding classes
- [x] Add the route + controller code sketch from the plan (from the existing `/hello/{name}` route and `HelloController::greet`)
- [x] Add **Commands**: `docker compose up`, `docker compose down`, `docker compose run --rm php composer install`, `docker compose run --rm php composer dump-autoload`
- [x] Add **Services and credentials (development only)**: `http://localhost`, `http://localhost:8080`, MariaDB port `3306` (host `mysql` from inside the containers), database `developmentdb`, user `developer`/`secret123`, root password `secret123`
- [x] Add **Spec workflow**: `specs/<id>/spec.md` (plus `plan.md`/`task.md`), skills `spec-init` → `spec-grill-me` → `spec-plan` → `spec-execute` in `.agents/skills/`, mirrored in `.claude/skills/`, `skills-lock.json` records sources
- [x] Do not add new conventions or a keep-in-sync rule

## CLAUDE.md

- [x] Create `CLAUDE.md` at the repo root containing only `@AGENTS.md`

## Verify

- [x] `AGENTS.md` doesn't contain `contact.php` or `hello.php` (e.g. `grep -nE "contact\.php|hello\.php" AGENTS.md` returns nothing)
- [x] `AGENTS.md` has no version numbers (no image tags, `8.5`, `^1.3`, `lts`, `5.2`, `1.30`)
- [x] Every path, command, URL and credential in `AGENTS.md` matches `docker-compose.yml`, `PHP.Dockerfile`, `nginx.conf`, `app/composer.json`, `.gitignore` and `app/public/index.php`
- [x] `CLAUDE.md` content is exactly `@AGENTS.md`
- [x] No other files were changed
