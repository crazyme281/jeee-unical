# JETSI

Journal of Engineering Technology and Sustainable Innovation, University of Calabar.

This repository contains the Laravel application, its Docker image, and scripts for setting up a local Windows development copy.

## Run locally (Windows)

The tested local environment uses XAMPP, PHP 8.5.12 or newer in the 8.5 series (x64 Thread Safe), and Composer.

Allow process execution:
```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
```

Install dependencies:
```powershell
.\scripts\install-windows.ps1 -XamppPath 'C:\xampp'
```

Configure database:
```
.\scripts\setup-local.ps1 -XamppPath 'C:\xampp'
```

Start site:
```
.\scripts\start-local.ps1
```

Stop site:
```powershell
.\scripts\stop-local.ps1
```

Open the printed address (default: http://127.0.0.1:8080). Stop the project web server from PowerShell with:

If XAMPP is already installed, the installer script keeps it and adds the project's PHP and Composer. These scripts use only a local MySQL database and do not deploy the site. For prerequisites, manual installation, custom paths or ports, and troubleshooting, see the [setup guide](SETUP.md).

## Deploy online

The provided deployment uses **Render** for the PHP/Apache Docker service and **Supabase** for PostgreSQL. Deployment instructions are included, but have not been tested against live services.

1. Push the project to a private Git repository. Do not include `.env`, credentials, database backups, or uploaded manuscripts.
2. Create a Supabase project and use its **Session pooler** connection details on port `5432` (not the transaction pooler on `6543`).
3. Create a Render Docker web service from this repository, or use `render.yaml` as a Blueprint. Attach a persistent disk at `/var/www/html/storage/app/public` for uploads.
4. Configure the production environment variables in Render. Set `APP_ENV=production`, `APP_DEBUG=false`, a generated `APP_KEY`, `DB_CONNECTION=pgsql`, `DB_SSLMODE=require`, and the Supabase session-pooler credentials. Keep secrets in Render, not in Git.
5. For a new empty database, set `RUN_MIGRATIONS=true` for the first deployment. After it succeeds, set it back to `false`. In the service Shell, seed settings and build the Backpack assets:

   ```sh
   php artisan db:seed --class=SettingsSeeder --force
   php artisan basset:cache
   ```

6. Register your account, then grant it admin access in the service Shell:

   ```sh
   php artisan journal:admin you@example.com
   ```

   Enable Row Level Security for application tables in Supabase. Verify registration, admin access, database-backed pages, and persistent uploads before launch.

The deployment template defaults `RUN_MIGRATIONS` to `false` to avoid changing a database unexpectedly. The detailed configuration, first-deploy checks, Docker-host alternative, custom domains, third-party services, and backup guidance are in the [setup guide](SETUP.md).

## Important

- Keep `composer.lock` and run `composer install`; do not run `composer update` as part of deployment.
- Back up the database and uploaded files separately. Never run `migrate:fresh`, `migrate:refresh`, or `migrate:reset` on a database with data to keep.