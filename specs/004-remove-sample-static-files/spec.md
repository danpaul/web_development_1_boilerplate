## Goal

Remove the sample static PHP files `app/public/contact.php` and `app/public/hello.php`, and update the README so it no longer refers to them.

## Requirements

- Delete `app/public/contact.php`.
- Delete `app/public/hello.php`.
- `README.md`, the sentence starting "Example usage is wired in `app/public/index.php`": replace the `hello.php` sample-script clause with a pointer to `HelloController` (`app/src/Controllers/HelloController.php`) and its `GET /hello/{name}` route as the example. Keep the mention that controller classes live under `app/src/Controllers/`.
- `README.md`, **NGINX** section: replace "Go to http://localhost/hello.php … hello world message." with an instruction to open [http://localhost](http://localhost), where the page shows "Welcome home!" (`HomeController::home`).
- Keep `HelloController`, `HomeController`, and the routes in `app/public/index.php` unchanged.
- Leave the existing spec and plan files under `specs/001-*`, `specs/002-*` and `specs/003-*` unchanged. They are historical records.

## Acceptance Criteria

- `app/public/` contains only `index.php`.
- `README.md` does not contain the strings `contact.php` or `hello.php`.
- `README.md` names `HelloController` and the `/hello/{name}` route as the example, and the NGINX section points to `http://localhost` with the "Welcome home!" output.
- No files outside `specs/` contain `contact.php` or `hello.php`, except `app/vendor/`.
- `http://localhost` still shows "Welcome home!" and `http://localhost/hello/Alice` still shows "Hi, Alice!".

## Out of Scope

- any other file changes
