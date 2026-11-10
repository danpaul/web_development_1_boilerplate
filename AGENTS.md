# AGENTS.md

PHP Docker boilerplate used for class learning and as the starting point for student projects.

## Project structure

- `docker-compose.yml`: services
- `PHP.Dockerfile`: php-fpm image with PDO/pdo_mysql and Composer
- `nginx.conf`: web server config
- `.env.example`: tracked default DB credentials. Copy to `.env` (gitignored), which Docker Compose reads
- `app/composer.json`, `app/composer.lock`: PHP dependencies and autoload
- `app/phinx.php`: Phinx config (env vars `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`)
- `app/db/migrations/`: Phinx migrations
- `app/db/seeds/`: Phinx seeders
- `app/db/seeds/ArticlesSeeder.php`: truncates `articles` and inserts 20 sample articles
- `app/vendor/`: generated and gitignored. The php container runs `composer install` on start if it is missing.
- `app/public/index.php`: front controller and route definitions
- `app/src/Controllers/`: `HomeController`, `HelloController`
- `specs/`: specs (`specs/<id>/spec.md`)
- `.agents/skills/`, `.claude/skills/`: spec workflow skills
- `skills-lock.json`: where each skill comes from

## Dependencies

- Services: nginx; php-fpm (with the PDO and pdo_mysql extensions, and Composer); MariaDB; phpMyAdmin
- Composer packages: nikic/fast-route, robmorgan/phinx

`docker-compose.yml`, `PHP.Dockerfile` and `app/composer.json` are the source of truth for versions.

## Request flow

- nginx serves `app/public`; requests that don't match a file go to `index.php`.
- FastRoute maps method + path to `[App\Controllers\X, 'method']`.
- The controller is instantiated and the method is called with the route variables as `$vars`.
- Unmatched route → 404; wrong method → 405.
- PSR-4: `App\` → `app/src/`. Run `dump-autoload` after adding classes or changing namespaces.

```php
// app/public/index.php
$r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);

// app/src/Controllers/HelloController.php
public function greet($vars = [])
{
    $name = $vars['name'] ?? 'World';
}
```

## Commands

```sh
cp .env.example .env
docker compose up
docker compose down
docker compose run --rm php composer install
docker compose run --rm php composer dump-autoload
docker compose run --rm php vendor/bin/phinx create CamelCaseName
docker compose run --rm php vendor/bin/phinx migrate
docker compose run --rm php vendor/bin/phinx rollback
docker compose run --rm php vendor/bin/phinx status
docker compose run --rm php vendor/bin/phinx seed:create CamelCaseName
docker compose run --rm php vendor/bin/phinx seed:run
docker compose run --rm php vendor/bin/phinx seed:run -s ArticlesSeeder
```

## Services and credentials (development only)

- App: `http://localhost`
- phpMyAdmin: `http://localhost:8080`
- MariaDB: port `3306` from the host. From inside the containers, use host `mysql`.
- Credentials come from `.env`. The defaults in `.env.example` are database `developmentdb`, user `developer` / `secret123`, root password `secret123`.

## Environment

- Never commit `.env`.
- New DB-related variables go in `.env.example` and in a `${VAR}` substitution in `docker-compose.yml`.

## Database changes

- Every schema change goes through a Phinx migration, never phpMyAdmin or hand-run SQL.
- Create migration files with `vendor/bin/phinx create CamelCaseName`, never by hand.
- Each migration makes one logical change.
- Ask the user before any destructive change (dropping tables or columns, or anything else that loses data).

## Database tables

- `articles` (migration `CreateArticlesTable`): `id`, `title`, `content`, `author`, `created_at`.
- Seed data: `ArticlesSeeder` inserts 20 articles by 3 authors with distinct `created_at` dates (2026-10-01 to 2026-10-20). It truncates `articles` on every run.

## Spec workflow

- Specs live in `specs/<id>/spec.md`. `spec-plan` adds `plan.md` and `task.md` next to the spec.
- Skills, in order: `spec-init` → `spec-grill-me` → `spec-plan` → `spec-execute`. They are in `.agents/skills/` and mirrored in `.claude/skills/`.
- `skills-lock.json` records each skill's source.
