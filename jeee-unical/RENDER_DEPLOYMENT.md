# Deploying jeee_unical to Render

Status of this package: **prepared, but not yet built or booted.** The Docker image and the
Laravel app could not be run in the environment where this was prepared (no Docker, no PHP 7.3).
Everything that *was* checked is listed in [section 9](#9-what-was-tested-and-what-was-not).
Treat your first Render deploy as the real test and follow [section 8](#8-first-deploy-checklist).

---

## 1. What this project is

| Item | Finding |
|---|---|
| Framework | Laravel **5.5.18** (`composer.lock`), admin panel by Backpack CRUD 3.2 |
| PHP | **7.3** (Laravel 5.5 / Symfony 3.3 / doctrine-dbal 2.6 do not run on PHP 8; your cPanel host used `ea-php73`) |
| Database | **MySQL** (`DB_CONNECTION=mysql`, utf8mb4). A `pgsql` block exists in config but the app was built on MySQL |
| Extensions needed | `pdo_mysql`, `gd` (Intervention Image), `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `curl` (Guzzle/Paystack); `exif`, `bcmath`, `opcache` also installed |
| Frontend | Pre-built assets are committed under `public/`. Views do **not** use `mix()` and there is no `mix-manifest.json`, so **no npm build step is run** (Laravel Mix 1 / webpack 2 will not build on a modern Node). If you ever edit `resources/assets`, build locally and commit the output in `public/`. |
| Queue / scheduler | `QUEUE_DRIVER=sync` and the scheduler is empty, so **no worker and no cron job are needed** |
| Third parties | Paystack (subscriptions), Google sign-in (Socialite), SMTP mail |
| Uploads | Manuscripts and issue covers are written with `Storage::put()` to the `local` disk, whose root is `storage/app/public` (`config/filesystems.php`). Backpack's file manager / TinyMCE use `public/uploads`. Downloads are served by `DownloadController` from `public/storage/...` |

### Project root
`jeee_unical/` (contains `artisan`, `composer.json`, `composer.lock`, `app/`, `bootstrap/`, `config/`, `public/`, `resources/`, `routes/`, `storage/`).
A second, **older copy** of the app was nested at `journal-app/journal-app/` in your upload (it differs from the root: older views, no `NewUser` notification, no `public/assets`). It is **not** in the prepared ZIP. Your original upload still has it.

---

## 2. Architecture on Render

```
Browser ──HTTPS──> Render edge proxy ──> Web Service (Docker: Apache + PHP 7.3 + Laravel)
                                              │            │
                                              │            └── Persistent Disk  → storage/app/public (uploads)
                                              └── MySQL over the internet (external provider)
```

* **Web Service (Docker)** built from `Dockerfile`; listens on Render's `$PORT` (default 10000).
* **Persistent Disk** mounted at `/var/www/html/storage/app/public`. Render's filesystem is otherwise ephemeral: anything written elsewhere (logs, sessions, cache, compiled views) disappears on every deploy/restart.
* **MySQL is external.** Render offers managed PostgreSQL and Key Value (Redis-compatible) but, according to Render's own support replies, no managed MySQL (check the current product list). Use any MySQL 5.7+/8.x (or MariaDB 10.2+) host that is reachable from the internet and ideally in a region near your Render region. Render does document running MySQL yourself as a paid private service with a disk, but you then own backups and upgrades.

> Cost note: persistent disks require a paid instance type (free instances cannot attach a disk). `render.yaml` uses `starter`. Free instances also block outbound SMTP ports 25/465/587.

---

## 3. Files added or changed

**Added**

| File | Purpose |
|---|---|
| `Dockerfile` | PHP 7.3.33 + Apache (Debian buster, apt repointed to archive.debian.org), extensions, Composer 2.2, `composer install --no-dev` from the lockfile, optimised autoloader |
| `docker/start.sh` | Container entrypoint (see below) |
| `docker/db-check.php` | Waits for MySQL before migrating |
| `docker/apache/*.conf` | Serve `public/` on `${PORT}`; deny PHP execution under `/storage` and `/uploads`; deny dotfiles; logs to stdout/stderr |
| `docker/php/zz-laravel.ini` | Production PHP settings; upload limits mirror your cPanel (210M/230M); memory 256M (cPanel had 32M) |
| `.dockerignore` | Keeps `.env`, `vendor`, uploads, logs, old backups out of the image |
| `.gitignore`, `.gitattributes` | Keeps secrets/uploads out of Git; forces LF endings for shell scripts (important on Windows) |
| `.env.example` | Template of every variable (no secrets) |
| `render.yaml` | Render Blueprint: Docker web service + disk + env vars |
| `RENDER_DEPLOYMENT.md` | This guide |

**Changed (3 small edits, no behaviour change for users)**

1. `app/Http/Middleware/TrustProxies.php`: `$proxies = '*'`. Render terminates HTTPS at its proxy; without this Laravel builds `http://` URLs and secure cookies/redirects misbehave.
2. `config/services.php`: a **hard-coded SparkPost secret** was replaced by `env('SPARKPOST_SECRET')`. SparkPost is not used (`MAIL_DRIVER=smtp`). If that value was ever a real key, rotate it, because it is in your old Git history/zips.
3. `config/database.php`: optional TLS option `MYSQL_ATTR_SSL_CA`. When the variable is not set the array is empty, so behaviour is identical to before.

**What `docker/start.sh` does on every start** (it never seeds, resets or drops anything):

1. Refuses to start if `APP_KEY` is missing.
2. Creates/fixes `storage/` and `bootstrap/cache/` directories and ownership (a freshly mounted disk is root-owned).
3. Links `public/storage -> storage/app/public` (`DownloadController` needs it) and `public/uploads -> storage/app/public/uploads` (so file-manager uploads share the persistent disk; Render allows one disk per service).
4. `php artisan package:discover` (registers Backpack, Intervention, Socialite…).
5. If `RUN_MIGRATIONS=true`: waits for the database, then `php artisan migrate --force` (applies only *pending* migrations). If this fails the container exits instead of serving a half-migrated app.
6. `php artisan config:cache` (`CACHE_CONFIG=false` disables it). `route:cache` is deliberately **not** used because `routes/api.php` contains closures.
7. `exec apache2-foreground` so Apache is PID 1 and handles Render's stop signal gracefully.

---

## 4. Environment variables

Set these in Render (Blueprint prompts for the ones marked *you set*; the rest are pre-filled in `render.yaml`).

| Variable | Value | Who sets it |
|---|---|---|
| `APP_KEY` | `base64:` + 32 random bytes, see 5.1 | **you set** (secret) |
| `APP_URL` | `https://<your-service>.onrender.com` or your custom domain, no trailing slash | **you set** |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | From your MySQL provider (your old local port `3308` is not used in production) | **you set** (secret) |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION` | SMTP account. Use `null` for no encryption, `tls` or `ssl` otherwise | **you set** (secret) |
| `GOOGLE_OAUTH_ID`, `GOOGLE_OAUTH_SECRET` | Google Cloud OAuth client | **you set** (secret) |
| `PAYSTACK_PUBLIC`, `PAYSTACK_SECRET` | Paystack keys (use the **live** keys only in production) | **you set** (secret) |
| `APP_ENV` | `production` | preset |
| `APP_DEBUG` | `false` (never `true` in production: it prints secrets) | preset |
| `APP_NAME` | `JETSI_APP` (taken from your old `.env`; change if wrong) | preset |
| `APP_LOG` / `APP_LOG_LEVEL` | `errorlog` / `error` (logs go to the Render log stream) | preset |
| `DB_CONNECTION` | `mysql` | preset |
| `SESSION_DRIVER` / `CACHE_DRIVER` / `QUEUE_DRIVER` / `BROADCAST_DRIVER` | `file` / `file` / `sync` / `log` (same as today; see section 6) | preset |
| `SESSION_SECURE_COOKIE` | `true` | preset |
| `RUN_MIGRATIONS` | `false` until you complete section 5.3 | preset |
| `CACHE_CONFIG` | `true` | preset |
| `MYSQL_ATTR_SSL_CA` | *Optional.* If your MySQL host requires TLS: `/etc/ssl/certs/ca-certificates.crt` (or the provider's CA file) | add manually |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | *Optional.* **Defaults in `config/mail.php` are `info@nigerianjournalofmedicine.com` / "Nigerian Journal of Medicine"**. Override if that is not your sender | add manually |
| `SPARKPOST_SECRET` | *Optional, unused.* | skip |

Never put real values in Git, in `render.yaml`, or in a ZIP.

---

## 5. Step-by-step deployment

### 5.1 Generate `APP_KEY`

Do **not** use Render's `generateValue` for this: it produces a string Laravel 5.5 rejects.

* macOS/Linux/Git Bash: `echo "base64:$(openssl rand -base64 32)"`
* Windows PowerShell:
  ```powershell
  $b = New-Object byte[] 32; [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($b); "base64:" + [Convert]::ToBase64String($b)
  ```
* Or, if you have PHP locally: `php artisan key:generate --show`

Nothing in the app stores encrypted data in the database (only cookies are encrypted), so a **new key is fine**. The only effect is that existing login sessions end. Keep the key safe; changing it later logs everyone out again.

### 5.2 Push the project to Git

Render deploys from a Git repository (GitHub/GitLab/Bitbucket). `render.yaml` must be in the repo root.

```bash
cd jeee_unical
git init
git add .
git status          # CHECK: .env, vendor/, storage uploads must NOT be listed
git commit -m "Prepare for Render"
git remote add origin <your-private-repo-url>
git push -u origin main
```

Use a **private** repository. Do not commit `.env`; `.gitignore` already excludes it.

### 5.3 Prepare the database (choose ONE path)

First create an empty MySQL database + user with your provider and note host, port, name, user, password. Make sure the provider allows connections from Render.

**Path A: fresh database (no existing data)**

1. Deploy with `RUN_MIGRATIONS=true` for the first deploy. All migrations in `database/migrations/` run (creates `users`, `manuscripts`, `files`, `authors`, `issues`, `articles`, `subscribers`, `subscriptions`, `settings`, …).
2. Open Render **Shell** for the service and seed the settings table **once**: `php artisan db:seed --class=SettingsSeeder`
3. Register an account on the site, then make it an admin (the admin check is `users.admin = 1`):
   ```sql
   UPDATE users SET admin = 1 WHERE email = 'you@example.com';
   ```
   The admin panel is at `/admin`.
4. Leaving `RUN_MIGRATIONS=true` is safe (only pending migrations run, nothing is dropped or reset), but you may set it back to `false` and run migrations manually after future releases.

**Path B: import your existing data**

1. Export from the current host (cPanel phpMyAdmin → Export, or):
   ```bash
   mysqldump --single-transaction --no-tablespaces -h <old-host> -u <user> -p <old_db> > dump.sql
   ```
2. Import into the **empty** new database (phpMyAdmin dumps usually contain `DROP TABLE IF EXISTS`, so never import into a database that already holds data you want to keep):
   ```bash
   mysql -h <new-host> -P <port> -u <user> -p --ssl-mode=REQUIRED <new_db> < dump.sql
   ```
   (Drop `--ssl-mode` if your provider does not use TLS.)
3. The dump contains the `migrations` table, so Laravel knows everything is already applied. **Keep `RUN_MIGRATIONS=false`.** After the first deploy, open Shell and run `php artisan migrate:status` to confirm every migration shows `Ran`.
4. Keep the old database untouched until the new site is verified.

### 5.4 Create the service

**Option 1: Blueprint (recommended)**

1. Render Dashboard → **New → Blueprint** → select your repo. Render reads `render.yaml`.
2. Fill in the prompted values (section 4, "you set").
3. Review the plan (`starter`), region (`frankfurt`, pick the region nearest your MySQL host) and disk (5 GB at `/var/www/html/storage/app/public`; choose a size with headroom and check Render's disk docs for resize rules). Apply.

**Option 2: manual**

New → Web Service → your repo → Runtime **Docker**, Dockerfile path `./Dockerfile`, instance type **Starter or higher**, Health Check Path `/robots.txt`, add a **Disk** (name `uploads`, mount path `/var/www/html/storage/app/public`), add all variables from section 4.

### 5.5 Import existing uploaded files (only with Path B)

Your original ZIP contained uploaded manuscripts/covers. They are **not** in the deploy ZIP (they are private user data and must not go into Git). They are in the separate `jeee_unical_uploads_data.zip`.

The app resolves stored file paths under `storage/app/public`. Copy the contents so the disk ends up like:

```
/var/www/html/storage/app/public/submissions/...   (manuscripts)
/var/www/html/storage/app/public/issues/...        (issue covers / PDFs)
/var/www/html/storage/app/public/articles/...
```

* Your upload had `storage/app/public/issues` (6 files) plus **legacy** folders `storage/submissions` (98 files) and `storage/issues` (2 files) outside `app/public`. With the current `filesystems.php` the code looks only under `storage/app/public`, so copy the legacy files into `storage/app/public/submissions` and `storage/app/public/issues` as well, then test a download from the site. Compare with the live server: whichever folder holds the files there is the one the database rows point to.
* Copy using Render's SSH access (Dashboard → your service → SSH / Connect; Render's SSH docs give the exact host name and the `scp`/`sftp` flags for your OpenSSH version), or use the Shell for small files.
* After copying, run `chown -R www-data:www-data /var/www/html/storage/app/public` in Shell.

### 5.6 Update external services

* **Google Cloud Console** → OAuth client → authorised redirect URI: `https://<your-domain>/auth/google/verify` (built from `APP_URL`).
* **Paystack dashboard**: update any callback/webhook URLs to the new domain.
* **Custom domain**: Render → Settings → Custom Domains; add the DNS records it shows, then set `APP_URL` to the final `https://` address and redeploy.

---

## 6. Production-readiness notes

| Area | State and recommendation |
|---|---|
| **Ephemeral filesystem** | Only the mounted disk survives deploys. `storage/logs`, `storage/framework/*`, `bootstrap/cache` and anything in the image are recreated on each deploy. Logs go to stderr (`APP_LOG=errorlog`) so nothing is lost there. |
| **Uploads** | Persisted on the disk (`storage/app/public`, plus `public/uploads` via symlink). A service with a disk runs as a **single instance** and, per Render's docs, deploys of such services are not zero-downtime; confirm current limits in Render's Persistent Disks documentation. For multi-instance scaling you would need object storage (S3); `config/filesystems.php` already has an `s3` disk, but `league/flysystem-aws-s3-v3` is **not** in `composer.json`, so that is a code change I did not make. |
| **Backups** | Back up the disk and the database yourself too (Render disk snapshots + provider backups). Uploaded manuscripts are not in the DB. |
| **Sessions** | `file` driver = sessions live in the container, so **users are logged out on each deploy/restart**. To keep them logged in: `php artisan session:table` (commit the new migration), run `php artisan migrate`, then set `SESSION_DRIVER=database`. Not enabled by default so a fresh import does not 500 because a `sessions` table is missing. |
| **Cache** | `file`, per container; fine for this app. |
| **Queue / scheduler** | `sync`, no scheduled tasks: no worker/cron service needed. Emails are sent *during* the web request, so a slow SMTP server slows that request. |
| **Mail** | Render blocks outbound SMTP ports **25, 465, 587** on free instances, and port 25 is blocked on paid ones too; 465/587 are allowed on paid instances (per Render's changelog and support replies). Your old settings used your cPanel mail host on the non-standard port `26`, which is not on that list, but I could not test that it is reachable from Render. If mail fails, use a transactional provider. Laravel 5.5 supports `MAIL_DRIVER=mailgun` with `MAILGUN_DOMAIN`/`MAILGUN_SECRET` (already referenced in `config/services.php`) with no code change. |
| **Database modes** | `config/database.php` enables `ONLY_FULL_GROUP_BY`/strict modes. If you import data from a more permissive server and some pages error with "GROUP BY" or "Incorrect … value", that is the cause; fix the query or relax `modes` deliberately. |
| **Health check** | `/robots.txt` is a static file, so it proves the container is listening but **not** that the database works. Note that Backpack Settings queries the DB on every web request, so a DB outage shows up as 500s on every page. |
| **Security headers / PHP in uploads** | Apache denies execution/serving of `*.php` under `/storage` and `/uploads` and blocks `.htaccess`/`.env`/`.git`. |
| **Secrets** | Your uploaded ZIP contained `.env` files with live-looking credentials (root, `journal-app/journal-app`, and `njm/`). They were excluded from the deploy ZIP. Because they have been shared outside your machine, **rotate** the database password, SMTP password, Google OAuth secret and Paystack secret keys when you move to production. |

---

## 7. Known limitations

* **End-of-life stack.** Laravel 5.5, PHP 7.3 and Debian buster no longer receive security fixes. This package reproduces your current runtime; it does not modernise it. Plan an upgrade (Laravel 5.5 → 6 LTS → … with PHP 7.4/8.x) and keep the admin area restricted. The Dockerfile works around the archived buster apt repositories; if you upgrade PHP, move to a supported base image.
* The `Dockerfile` pins `php:7.3.33-apache-buster`. If Docker Hub ever removes that tag, the build will fail at the first line.
* Laravel Mix assets are not rebuilt (see section 1).

---

## 8. First deploy checklist

1. Render build log ends with the image pushed, no Composer errors.
2. Deploy log shows `[start] Starting Apache on port 10000` (or your `$PORT`). If you see `APP_KEY is not set`, add it.
3. Open `https://<service>.onrender.com/`: home page renders with CSS/JS and **https** asset URLs (no mixed-content warnings).
4. Register / log in / log out. Password reset email is delivered.
5. Google sign-in works (redirect URI registered).
6. `/admin` loads for an admin user; create an issue with a cover image; upload a manuscript.
7. Open the manuscript/article download from the site (this exercises `public/storage` and the disk).
8. Trigger a deploy (or manual restart) and confirm the uploaded file is still there.
9. Make a small Paystack **test** transaction if you use subscriptions.
10. Only after all of the above would I call it production-ready.

Run one-off commands in Render **Shell** (paid services): `php artisan migrate:status`, `php artisan migrate --force`, `php artisan db:seed --class=SettingsSeeder` (fresh DB only), `php artisan config:clear`.

---

## 9. What was tested and what was not

**Passed (in the preparation environment)**

* `composer validate` on `composer.json`/`composer.lock` (one pre-existing warning: exact version `cviebrock/eloquent-sluggable 4.3`).
* `composer install --no-dev` from `composer.lock` resolves and downloads all 66 production packages (run with `--ignore-platform-reqs` on a PHP 8.3 host, so this proves the lockfile/downloads, not PHP 7.3 compatibility).
* The `vendor/` folder from your upload matches a clean lockfile install (only line-ending differences, no code edits), so shipping `vendor/` is unnecessary and the image installs it itself.
* `php -l` syntax check of every PHP file I added or changed.
* `docker/start.sh`: `sh -n`, plus 8 behavioural scenarios and 3 `public/uploads` link checks with stubbed `php`/`apache2-foreground` (refuses without `APP_KEY`; default port; migrations off/on; DB down; migration failure never starts Apache; `CACHE_CONFIG=false`; non-MySQL skips the probe; existing `public/uploads` migrated to the disk; second boot idempotent).
* Apache configuration with real Apache 2.4: syntax OK; listens on `${PORT}`; static files; `.htaccess` rewrite to the front controller; `public/storage` symlink followed; `*.php` under `/storage` returns 403 (control run without the rule executed the file); directory listing off; dotfiles denied. (Run with Ubuntu's Apache and a PHP 8.3 module standing in for 7.3, using a stub `index.php`.)
* `hadolint` on the Dockerfile: only warning DL3008 (apt packages not version-pinned; intentional with an archived repo).
* `render.yaml` parses as valid YAML and uses documented Blueprint fields.
* Final ZIP contents scanned: no `.env`, no credentials, no `vendor/`, no uploads.

**Could not be run**

* `docker build` / `docker run` (no Docker in the preparation environment; Docker Hub unreachable). The `apt`/`docker-php-ext-*` layers, especially the `gd` configure flags for PHP 7.3 and the buster archive workaround, are **untested**.
* Laravel itself on PHP 7.3: `artisan` boot, `package:discover`, `config:cache`, `migrate`, any page render, image upload (GD), Paystack, Google sign-in, email.
* Render's own Blueprint validator (`render blueprints validate` / JSON schema), a real Render deploy, persistent-disk behaviour, proxy/HTTPS behaviour in practice.
* Any connection to a real MySQL server.

---

## 10. Troubleshooting

| Symptom | Likely cause / fix |
|---|---|
| Build fails at the first `apt-get update` with 404s | Debian buster mirrors moved. The Dockerfile already points to `archive.debian.org`; if it still fails, check that `/etc/apt/sources.list` in the base image matches the `sed` patterns. |
| Build fails in `docker-php-ext-configure gd` | PHP 7.3 uses `--with-freetype-dir/--with-jpeg-dir`; if you change the PHP version, those flags change (`--with-freetype --with-jpeg` on 7.4+). |
| Build fails in `composer install` | Read the Composer error. Network/GitHub rate limit: retry; a missing PHP extension: add it to the Dockerfile. |
| Container exits: `APP_KEY is not set` | Add `APP_KEY` (section 5.1). |
| 500 on every page, logs say `SQLSTATE[HY000] [2002]` / `[1045]` / `[1049]` | Wrong `DB_HOST`/`DB_PORT`, credentials, or database name; or provider firewall blocks Render. For `[2002]` with TLS-only providers set `MYSQL_ATTR_SSL_CA`. |
| 500 with "unsupported cipher" / "No application encryption key" | `APP_KEY` malformed. It must be `base64:` + 32 bytes encoded, or exactly 32 characters. Redeploy after fixing (config is cached at start). |
| `Base table or view already exists` during migrate | You imported a dump without the `migrations` table, or imported and set `RUN_MIGRATIONS=true`. Set `RUN_MIGRATIONS=false` and check `php artisan migrate:status`. |
| `Access denied` on uploads / cannot write to `storage` | Disk mounted root-owned. `start.sh` re-chowns on every start; also run `chown -R www-data:www-data /var/www/html/storage` in Shell. |
| Uploaded files vanish after deploy | They were written outside the disk. Only `storage/app/public` (and `public/uploads` via symlink) persist. Confirm the disk's mount path exactly matches `render.yaml`. |
| Downloads return 404/500 | `public/storage` symlink missing (check deploy log for `storage:link`), or files were not copied to `storage/app/public/...` (section 5.5). |
| Links/assets use `http://` or redirect loops | `APP_URL` not `https://…`, or `TrustProxies` change missing. |
| Config changes have no effect | Config is cached at start; change the env var in Render (which restarts) or set `CACHE_CONFIG=false`. |
| Emails time out | SMTP port blocked (section 6, Mail) or wrong host; switch provider/driver. |
| Admin redirects to `/` | The user's `admin` column is not `1` (section 5.3). |
| `ERROR 1115` / charset issues on import | Import into a database created with `utf8mb4` / `utf8mb4_unicode_ci`. |
| Deploy goes "unhealthy" | Check the deploy log: health check path must return 200 on `$PORT`; `/robots.txt` is a static file in `public/`. |

---

## 11. Rolling back / maintaining

* Render lets you roll back to a previous deploy from the service's deploy/events page. Database migrations are not rolled back automatically; `php artisan migrate:rollback` is destructive, so take a database backup first.
* Never run `migrate:fresh`, `migrate:refresh`, `migrate:reset` or `db:wipe` against production.
* To add a new migration later: commit it, deploy with `RUN_MIGRATIONS=true` (or run `php artisan migrate --force` in Shell).
