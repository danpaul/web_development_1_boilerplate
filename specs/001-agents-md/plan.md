# Plan: AGENTS.md and CLAUDE.md

Spec: [./spec.md](./spec.md)

Add a root `AGENTS.md` describing the repo as it is now, and a root `CLAUDE.md` that imports it.

## Technical context

- Stack, from `docker-compose.yml` and `PHP.Dockerfile`:
  - `nginx`: port `80:80`, mounts `./nginx.conf` and `./app` → `/app`
  - `php`: built from `PHP.Dockerfile` (php-fpm, `pdo` + `pdo_mysql`, Composer, `WORKDIR /app`). On start it runs `composer install` if `vendor/autoload.php` is missing, then php-fpm.
  - `mysql`: MariaDB, port `3306:3306`, `MARIADB_DATABASE=developmentdb`, `MARIADB_USER=developer`, `MARIADB_PASSWORD=secret123`, `MARIADB_ROOT_PASSWORD=secret123`, volume `mysqldata`
  - `phpmyadmin`: port `8080:80`, `PMA_HOST=mysql`
- `nginx.conf`: `root /app/public`; `try_files $uri $uri/ /index.php?$args`; `*.php` → `fastcgi_pass php:9000`.
- `app/composer.json`: requires `nikic/fast-route`; PSR-4 `App\` → `src/` (i.e. `app/src/`).
- `.gitignore` ignores `/app/vendor/`. `app/composer.lock` is tracked.
- `app/public/index.php`: FastRoute `simpleDispatcher` with routes:
  - `GET /` → `App\Controllers\HomeController::home`
  - `GET /hello/{name}` → `App\Controllers\HelloController::greet`
  - FOUND: `new $class()`, then `$controller->$method($vars)` with `$vars = $routeInfo[2]`. NOT_FOUND → 404 `Not Found`; METHOD_NOT_ALLOWED → 405 `Method Not Allowed`.
- Controllers: `app/src/Controllers/HomeController.php`, `HelloController.php`. Each method has the signature `method($vars = [])`.
- Skills: `spec-init`, `spec-grill-me`, `spec-plan`, `spec-execute` live in `.agents/skills/<name>/SKILL.md`. `.claude/skills/` holds identical copies. `skills-lock.json` records each skill's `source` (`danpaul/agent_skills_sdd`, GitHub), `skillPath` and hash.
- Neither `AGENTS.md` nor `CLAUDE.md` exists yet.

## Target structure

- Add `AGENTS.md` (repo root)
- Add `CLAUDE.md` (repo root)
- No other files change.

## AGENTS.md content

Use these sections, in this order:

1. `# AGENTS.md` and a one-line intro: PHP Docker boilerplate used for class learning and as the starting point for student projects.
2. `## Project structure` (bullets, one per item):
   - `docker-compose.yml`: services
   - `PHP.Dockerfile`: php-fpm image with PDO/pdo_mysql and Composer
   - `nginx.conf`: web server config
   - `app/composer.json`, `app/composer.lock`: PHP dependencies and autoload
   - `app/vendor/`: generated and gitignored. The php container runs `composer install` on start if it is missing.
   - `app/public/index.php`: front controller and route definitions
   - `app/src/Controllers/`: `HomeController`, `HelloController`
   - `specs/`: specs (`specs/<id>/spec.md`)
   - `.agents/skills/`, `.claude/skills/`: spec workflow skills
   - `skills-lock.json`: where each skill comes from
   - Do not list `README.md` or any other file in `app/public/`.
3. `## Dependencies`: names only, no versions:
   - nginx; php-fpm (with the PDO and pdo_mysql extensions, and Composer); MariaDB; phpMyAdmin
   - Composer package: nikic/fast-route
   - One line saying `docker-compose.yml`, `PHP.Dockerfile` and `app/composer.json` are the source of truth for versions.
4. `## Request flow`:
   - nginx serves `app/public`; requests that don't match a file go to `index.php`.
   - FastRoute maps method + path to `[App\Controllers\X, 'method']`.
   - The controller is instantiated and the method is called with the route variables as `$vars`.
   - Unmatched route → 404; wrong method → 405.
   - PSR-4: `App\` → `app/src/`. Run `dump-autoload` after adding classes or changing namespaces.
   - Code sketch, taken from the existing code:

     ```php
     // app/public/index.php
     $r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);

     // app/src/Controllers/HelloController.php
     public function greet($vars = [])
     {
         $name = $vars['name'] ?? 'World';
     }
     ```
5. `## Commands`: code block with `docker compose up`, `docker compose down`, `docker compose run --rm php composer install`, `docker compose run --rm php composer dump-autoload`.
6. `## Services and credentials (development only)`:
   - App: `http://localhost`
   - phpMyAdmin: `http://localhost:8080`
   - MariaDB: port `3306` from the host. From inside the containers, use host `mysql`.
   - Database `developmentdb`, user `developer` / `secret123`, root password `secret123`
7. `## Spec workflow`:
   - Specs live in `specs/<id>/spec.md`. `spec-plan` adds `plan.md` and `task.md` next to the spec.
   - Skills, in order: `spec-init` → `spec-grill-me` → `spec-plan` → `spec-execute`. They are in `.agents/skills/` and mirrored in `.claude/skills/`.
   - `skills-lock.json` records each skill's source.
   - Do not add a keep-in-sync rule.

Constraints:

- No version numbers anywhere: no image tags, no `8.5`, no `^1.3`.
- The strings `contact.php` and `hello.php` must not appear. The route `/hello/{name}` is allowed.
- Add no conventions beyond what the repo already does.

## CLAUDE.md content

Exactly one line:

```
@AGENTS.md
```

## Implementation order

1. Write `AGENTS.md` following the sections above.
2. Write `CLAUDE.md`.
3. Verify (see `task.md`).

## Out of scope

- Establishing new code conventions or tailoring the repo to a specific project
- Editing `README.md`, `app/public/*`, controllers, Docker/nginx config or skills
- Documenting `autoload-dev` / `tests/` (the folder doesn't exist)
