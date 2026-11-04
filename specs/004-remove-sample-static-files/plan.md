# Plan: Remove sample static files

Spec: [./spec.md](./spec.md)

Delete `app/public/contact.php` and `app/public/hello.php`, and update two README passages to point to the FastRoute controllers instead.

## Technical context

- `app/public/` is tracked in git with three files: `contact.php`, `hello.php`, `index.php`.
- `nginx.conf`: `root /app/public`; `try_files $uri $uri/ /index.php?$args`. After the deletion, `/hello.php` and `/contact.php` fall through to `index.php` and return FastRoute's 404 `Not Found`.
- `app/public/index.php` routes:
  - `GET /` → `App\Controllers\HomeController::home` → echoes `Welcome home!`
  - `GET /hello/{name}` → `App\Controllers\HelloController::greet` → echoes `Hi, {name}!`
- References to `contact.php` / `hello.php` outside `specs/` and `app/vendor/` (from `grep`):
  - `app/public/contact.php:4` and `app/public/hello.php:10` (go away with the deletion)
  - `README.md:68` (Composer Autoload section)
  - `README.md:74` (NGINX section)
- `AGENTS.md` already has no references.

## Target structure

- Delete `app/public/contact.php`
- Delete `app/public/hello.php`
- Change `README.md` (two lines only)
- No other files change.

## README.md changes

Line 68, current:

```md
Example usage is wired in `app/public/index.php`. A sample script is at `app/public/hello.php`, and controller classes live under `app/src/Controllers/`.
```

Replace with:

```md
Example usage is wired in `app/public/index.php`: the `GET /hello/{name}` route calls `HelloController` (`app/src/Controllers/HelloController.php`) as an example. Controller classes live under `app/src/Controllers/`.
```

Line 74 (NGINX section), current:

```md
Go to [http://localhost/hello.php](http://localhost/hello.php). You should see a hello world message.
```

Replace with:

```md
Go to [http://localhost](http://localhost). You should see "Welcome home!" (from `HomeController::home`).
```

Leave the line before it (`NGINX will now serve files in the app/public folder.`) unchanged.

## Implementation order

1. `git rm app/public/contact.php app/public/hello.php`
2. Edit the two `README.md` lines as above.
3. Verify (see `task.md`): file listing, greps, and the two URLs with the containers running.

## Out of scope

- Changing `HelloController`, `HomeController`, the routes in `app/public/index.php`, or `nginx.conf`
- Editing `specs/001-*`, `specs/002-*`, `specs/003-*`
- Editing `AGENTS.md` or any other README section
- Adding replacement examples or routes for the deleted scripts
