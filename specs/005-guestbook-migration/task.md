# Tasks: Guestbook migration

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Migration

- [x] Run `docker compose run --rm php vendor/bin/phinx create CreateGuestbook2Table`. Do not write the file by hand.
- [x] Confirm exactly one new file, `app/db/migrations/<timestamp>_create_guestbook2_table.php`, with `final class CreateGuestbook2Table extends AbstractMigration`
- [x] In `change()`, build `$this->table('guestbook_2')` with no `id` options, so Phinx adds an unsigned auto-increment `id` primary key
- [x] Add `posted_at`: `datetime`, `['default' => 'CURRENT_TIMESTAMP']`
- [x] Add `name`: `string`, `['limit' => 32]`
- [x] Add `email`: `string`, `['limit' => 63]`
- [x] Add `message`: `string`, `['limit' => 256]`
- [x] Add `->addIndex(['posted_at'])`, with no `unique` option and no custom name
- [x] End with `->create()`, not `save()`. Set no `'null' => true` anywhere.
- [x] Keep the rest of the generated template unchanged. Do not change `app/phinx.php`, `GuestbookController` or anything else.

## Verification cycle

- [x] `docker compose up -d mysql` if it is not running
- [x] `phinx status` lists `CreateGuestbook2Table` as `down`
- [x] `phinx migrate` succeeds
- [x] `SHOW CREATE TABLE guestbook_2` (via `docker compose exec mysql mariadb ...`) shows `id` int unsigned AUTO_INCREMENT PK, `posted_at` datetime NOT NULL DEFAULT current_timestamp(), varchar(32)/(63)/(256) NOT NULL for `name`/`email`/`message`, a non-unique `KEY posted_at`, and `utf8mb4`
- [x] `phinx rollback` succeeds
- [x] `SHOW TABLES LIKE 'guestbook_2'` returns nothing, and `phinx status` shows the migration as `down`

## Verify

- [x] `app/db/migrations/` has `.gitkeep` and exactly one migration file
- [x] `guestbook_2` does not exist in the dev DB (the cycle ended on rollback)
- [x] `git status` shows only the new migration file (plus the spec folder)
