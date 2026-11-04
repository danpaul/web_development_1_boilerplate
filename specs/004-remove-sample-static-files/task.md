# Tasks: Remove sample static files

Plan: [./plan.md](./plan.md) · Spec: [./spec.md](./spec.md)

## Delete sample files

- [x] `git rm app/public/contact.php app/public/hello.php`
- [x] Do not touch `app/public/index.php`

## README.md

- [x] Replace the line starting "Example usage is wired in `app/public/index.php`" with the exact text from the plan (`GET /hello/{name}` route, `HelloController` at `app/src/Controllers/HelloController.php`, controller classes under `app/src/Controllers/`)
- [x] In **NGINX**, replace the `http://localhost/hello.php` line with the exact text from the plan (`http://localhost`, "Welcome home!", `HomeController::home`)
- [x] Do not change any other README line

## Verify

- [x] `ls app/public` lists only `index.php`
- [x] `grep -nE "contact\.php|hello\.php" README.md` returns nothing
- [x] `README.md` names `HelloController` and `/hello/{name}`, and the NGINX section points to `http://localhost` with "Welcome home!"
- [x] `git grep -nE "contact\.php|hello\.php" -- . ':!specs'` returns nothing
- [x] With `docker compose up` running: `curl http://localhost` returns `Welcome home!` and `curl http://localhost/hello/Alice` returns `Hi, Alice!`
- [x] `git status` shows only the two deletions, `README.md`, and `specs/004-remove-sample-static-files/`. `HelloController`, `HomeController`, `index.php` and `specs/001-*`–`003-*` are unchanged
