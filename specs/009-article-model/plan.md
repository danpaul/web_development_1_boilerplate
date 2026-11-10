# Plan: Article model

Spec: [./spec.md](./spec.md)

Add a data-only `App\Models\Article` class whose public typed properties mirror the `articles` table, regenerate the autoloader, check it once with `PDO::FETCH_CLASS`, and document the model conventions in `README.md` and `AGENTS.md`.

## Technical context

- PHP `>=8.5` (`app/composer.json`); PSR-4 `App\` → `app/src/` already configured. Only `app/src/Controllers/` exists today.
- `articles` table (`app/db/migrations/20261110131728_create_articles_table.php`):
  - `id`: implicit unsigned auto-increment primary key
  - `title`: `string(255)`, NOT NULL
  - `content`: `text`, NOT NULL
  - `author`: `string(255)`, NOT NULL
  - `created_at`: `datetime`, default `CURRENT_TIMESTAMP`, NOT NULL
- `ArticlesSeeder` provides 20 rows (needed for the manual check).
- The app has no shared PDO connection yet; the manual check builds its own from the `DB_*` env vars the php service already receives.
- PDO `FETCH_CLASS` assigns column values to properties before the constructor runs; with no constructor, nothing overwrites them. Because every column has a declared property, no dynamic properties are created (no PHP 8.2+ deprecation).
- `pdo_mysql` on PHP 8.1+ returns `id` as `int` and `datetime` as a string like `2026-10-01 09:00:00`, which match the property types.

## Target structure

- Add `app/src/Models/Article.php`
- Change `README.md` (new `### Models` subsection)
- Change `AGENTS.md` (Project structure, new `## Models` section)
- No other files change (no controller, route, migration, Composer or Docker changes)

## Article class

```php
<?php

namespace App\Models;

class Article
{
    public ?int $id = null;
    public string $title;
    public string $content;
    public string $author;
    public ?string $created_at = null;
}
```

- Exactly these five properties, in column order, same names (`created_at` stays snake_case).
- No constructor, no methods, no `readonly`, no `final`, no PDO/DB code.
- Header style matches the controllers (no `declare(strict_types=1)`).
- `title`, `content`, `author` have no defaults: they are uninitialized on `new Article()` until assigned or fetched.

After adding the file:

```sh
docker compose run --rm php composer dump-autoload
```

## Manual check (not committed)

Requires `mysql` running, migrations applied and `ArticlesSeeder` run. Pipe the script to PHP via stdin so no file is created:

```sh
docker compose run --rm -T php php <<'PHP'
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'vendor/autoload.php';

use App\Models\Article;

$pdo = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4',
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$rows = $pdo->query('SELECT * FROM articles ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$articles = $pdo->query('SELECT * FROM articles ORDER BY id')->fetchAll(PDO::FETCH_CLASS, Article::class);

echo count($articles), PHP_EOL;                                   // 20
var_dump($articles[0] instanceof Article);                        // true
$same = true;
foreach ($articles as $i => $a) {
    $same = $same && get_object_vars($a) === $rows[$i];
}
var_dump($same);                                                  // true
var_dump(array_keys(get_object_vars(new Article())));             // id, created_at (others uninitialized)
$new = new Article();
var_dump($new->id, $new->created_at);                             // NULL, NULL
PHP
```

- Pass: prints `20`, `true`, `true`, `NULL`, `NULL`, and no warnings or `Deprecated: Creation of dynamic property` messages.
- If the strict `===` comparison fails only on types, inspect `var_dump($rows[0])` vs `var_dump($articles[0])` before changing anything.

## README.md

Add `### Models` under `## Usage`, right after `### Composer Autoload` (before `### NGINX`):

- Models live in `app/src/Models/` under the `App\Models` namespace (example: `App\Models\Article`).
- A model's property names and types match its table's columns exactly, as defined by the migration (snake_case kept, e.g. `created_at`). Auto-filled columns (`id`, `created_at`) are nullable with a `null` default, so `new Article()` works for an unsaved row.
- Models hold data only (no database access).
- Short example:

  ```php
  $stmt = $pdo->query('SELECT * FROM articles');
  $articles = $stmt->fetchAll(PDO::FETCH_CLASS, Article::class);
  ```

## AGENTS.md

- Project structure: add after the `app/src/Controllers/` line:
  - `` `app/src/Models/`: `Article` ``
- New `## Models` section after `## Database tables`:
  - Models live in `app/src/Models/` under the `App\Models` namespace.
  - Property names and types match the table's columns exactly, as defined by the migration; no extra or missing properties. Auto-filled columns are nullable with a `null` default.
  - Models are data only and can be loaded with `PDO::FETCH_CLASS` (`$stmt->fetchAll(PDO::FETCH_CLASS, Article::class)`); no constructor arguments.

## Implementation order

1. Create `app/src/Models/Article.php`.
2. `docker compose run --rm php composer dump-autoload`.
3. Ensure DB is migrated and seeded (`phinx status`; `phinx seed:run -s ArticlesSeeder` if needed).
4. Run the manual check.
5. Update `README.md`.
6. Update `AGENTS.md`.

## Out of scope

- Any further code changes: no repositories/services, no shared PDO connection class, no controllers, routes or views using `Article`
- Getters/setters, validation, `DateTime` conversion, or save/find methods on the model
- Schema or seed changes, new migrations, Composer/Docker/env changes
- Committing the manual check script or any tests
