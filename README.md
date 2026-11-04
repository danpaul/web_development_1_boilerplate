# Docker template for PHP projects
This repository provides a starting template for PHP application development.

It contains:
* NGINX webserver
* PHP FastCGI Process Manager with PDO MySQL support
* MariaDB (GPL MySQL fork)
* PHPMyAdmin
* Composer
* Composer package [nikic/fast-route](https://github.com/nikic/FastRoute) for routing

## Setup

1. Install Docker Desktop on Windows or Mac, or Docker Engine on Linux.
1. Clone the project
1. Copy the env file:
   ```bash
   cp .env.example .env
   ```

## Environment variables

Docker Compose reads `.env` and passes the values to the php, mysql and phpMyAdmin services.

| Variable | What it is for |
| --- | --- |
| `DB_HOST` | Database host as seen from the containers (the `mysql` service). Used by PHP/Phinx and phpMyAdmin. |
| `DB_NAME` | Database created by MariaDB and used by the app. |
| `DB_USER` | The app's database user, also used for the phpMyAdmin login. |
| `DB_PASSWORD` | Password for `DB_USER`, also used for the phpMyAdmin login. |
| `DB_ROOT_PASSWORD` | MariaDB root password. |

`.env` is gitignored and must never be committed. `.env.example` is the tracked template.

Without `.env`, Docker Compose warns that the variables are not set and uses blank values, so the database services will not work. Run `cp .env.example .env` to fix it.

MariaDB applies the credentials only when the `mysqldata` volume is first created. To change them later, run the command below, which **deletes all database data**, then run `docker compose up` again:

```bash
docker compose down -v
```

## Usage

In a terminal, from the cloned project folder, run:
```bash
docker compose up
```

### Composer Autoload

This template is configured to use Composer for PSR-4 autoloading:

- Namespace `App\\` is mapped to `app/src/`.

To install dependencies and generate the autoloader, run:

```bash
docker compose run --rm php composer install
```

If you add new classes or change namespaces, regenerate the autoloader:

```bash
docker compose run --rm php composer dump-autoload
```

Example usage is wired in `app/public/index.php`: the `GET /hello/{name}` route calls `HelloController` (`app/src/Controllers/HelloController.php`) as an example. Controller classes live under `app/src/Controllers/`.

### NGINX

NGINX will now serve files in the app/public folder.

Go to [http://localhost](http://localhost). You should see "Welcome home!" (from `HomeController::home`).

### PHPMyAdmin

PHPMyAdmin provides basic database administration. It is accessible at [localhost:8080](localhost:8080).

Log in with `DB_USER` / `DB_PASSWORD` from `.env` (default `developer` / `secret123`).


### Stopping the docker container

If you want to stop the containers, press Ctrl+C. 

Or run:
```bash
docker compose down
```

## Database migrations

Database schema changes are made with [Phinx](https://phinx.org) migrations. The Phinx config is `app/phinx.php` and migration files live in `app/db/migrations/`. Phinx runs inside the php container, so every command starts with `docker compose run --rm php`.

### Commands

Create a new migration file. The name must be CamelCase:

```bash
docker compose run --rm php vendor/bin/phinx create CreateUsersTable
```

Apply all migrations that haven't been run yet:

```bash
docker compose run --rm php vendor/bin/phinx migrate
```

Undo the last migration:

```bash
docker compose run --rm php vendor/bin/phinx rollback
```

List all migrations and whether each one is up (applied) or down (not applied):

```bash
docker compose run --rm php vendor/bin/phinx status
```

### Migration file structure

- **Filename:** `YYYYMMDDHHMMSS_snake_case_name.php`, for example `20261104120000_create_users_table.php`. The timestamp sets the order in which migrations run. Don't rename a file after it has been run.
- **Class:** the CamelCase version of the name (`CreateUsersTable`), extending `Phinx\Migration\AbstractMigration`.
- **`change()`:** write only the forward change. Phinx works out how to reverse it on rollback. This works for creating tables and adding or renaming columns, indexes and foreign keys.
- **`up()` / `down()`:** use these instead of `change()` when Phinx can't reverse the change automatically, for example raw SQL or data changes. `up()` makes the change and `down()` must undo it.

Example migration using `change()`:

```php
<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePostsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('posts')
            ->addColumn('title', 'string', ['limit' => 150, 'null' => false])
            ->addColumn('body', 'text', ['null' => true])
            ->addColumn('views', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('published', 'boolean', ['default' => false])
            ->addColumn('published_at', 'datetime', ['null' => true])
            ->addColumn('price', 'decimal', ['precision' => 8, 'scale' => 2])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addTimestamps()
            ->addIndex(['title']) // use ['unique' => true] as 2nd argument for a unique index
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE'])
            ->create();
    }
}
```

Example using `up()` and `down()`:

```php
public function up(): void
{
    $this->execute("UPDATE posts SET published = 1 WHERE published_at IS NOT NULL");
}

public function down(): void
{
    $this->execute("UPDATE posts SET published = 0 WHERE published_at IS NOT NULL");
}
```

### Columns and options

**Implicit `id`:** every table automatically gets an auto-increment, unsigned integer primary key called `id`. Pass `['id' => false]` to `$this->table('name', ['id' => false])` to leave it out.

Common column types:

- `string`: short text such as names, titles and emails (`VARCHAR`, 255 characters unless you set `limit`)
- `integer`: whole numbers such as counts and ids of other rows
- `text`: long text such as descriptions and post bodies
- `boolean`: true/false flags (stored as `TINYINT(1)`)
- `datetime`: a date and time
- `decimal`: exact numbers with a fixed number of decimals, such as prices. Set `precision` (total digits) and `scale` (digits after the decimal point).

Common column options:

- `limit`: maximum length, for example `['limit' => 100]` for a `string`
- `null`: whether the column may be empty (`NULL`). In this Phinx version, columns allow `NULL` by default, so pass `['null' => false]` for required columns.
- `default`: the value used when none is given, for example `['default' => 0]`
- `signed`: `['signed' => false]` makes an integer unsigned (no negative numbers). Use it for ids and counters.

**`addTimestamps()`:** adds `created_at` (set when the row is inserted) and `updated_at` (set when the row is updated) columns.

**Indexes:** `->addIndex(['email'])` speeds up searching on a column. `->addIndex(['email'], ['unique' => true])` also makes sure no two rows have the same value.

**Foreign keys:** `->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])` links `user_id` to `users.id`. The column must have the same type as the referenced `id`, so use `'integer'` with `['signed' => false]`. The `delete` and `update` options set what happens to this row when the referenced row is deleted or its id changes: `CASCADE`, `SET_NULL`, `RESTRICT` or `NO_ACTION`.

**Changing an existing table:** use `->update()` instead of `->create()`:

```php
$this->table('posts')
    ->addColumn('slug', 'string', ['limit' => 150])
    ->update();
```
