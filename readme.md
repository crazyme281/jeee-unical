# JETSI

Journal of Engineering Technology and Sustainable Innovation, University of Calabar.

## Requirements

| Item | Version used |
| --- | --- |
| Windows | Windows 11 x64 |
| XAMPP | 8.5.12 |
| Apache | 2.4.65 |
| MariaDB | 10.11.16 |
| PHP | 8.5.12, Windows build 8.5.12-1, x64 Thread Safe |
| Composer | 2.11.2 |
| Laravel | 13.42.0 |
| Backpack | 7.1.26 |

Use PHP 8.5.12 or newer within the 8.5 series. Do not use the original Windows 8.5.12 build. Use 8.5.12-1 or a newer official build. Check [php.net](https://www.php.net/downloads.php) before installing.

Keep `composer.lock`. `composer install` uses the saved package versions. `composer update` changes them and is not part of setup.

An older PHP can remain installed. The commands below select PHP by its full path. No change to the computer's default PHP is required.

You need an internet connection and at least 5 GB free for the Windows installation. No Node installation or page build is needed. The page files are already in `public`.

Local XAMPP setup has been tested. The Supabase, Render, and other Docker deployment instructions have not been tested on a live service.

## Install on Windows

### 1. Install XAMPP and the Windows runtime

Download [XAMPP 8.5.12 for Windows](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.5.12/). Run its installer and select Apache, MySQL, PHP, and phpMyAdmin. This guide uses `C:\xampp`. If XAMPP is already installed elsewhere, keep it and use its actual path.

Install [Microsoft Visual C++ 2015-2022 x64](https://aka.ms/vs/17/release/vc_redist.x64.exe) if it is not already installed. Approve the Windows permission prompt. Do not disable Windows security settings.

Do not start Apache yet. MySQL in the XAMPP control panel is MariaDB, which works with the project's local MySQL connection.

### 2. Add the corrected PHP build

Open PowerShell. Download the official Windows x64 Thread Safe build and check it before extracting it.

```powershell
$Xampp = 'C:\xampp'
$Download = Join-Path $env:USERPROFILE 'Downloads\php-8.5.12-1.zip'
curl.exe --fail --location --output $Download 'https://downloads.php.net/~windows/releases/archives/php-8.5.12-1-Win32-vs17-x64.zip'
if ($LASTEXITCODE -ne 0) { throw 'PHP download failed' }
$Expected = '0dacd6484b092b5e9ae9fa3d7f96a937caf9d874eee2d980b68ed3129741144a'
if ((Get-FileHash $Download -Algorithm SHA256).Hash -ne $Expected) { throw 'PHP download check failed' }
Expand-Archive -LiteralPath $Download -DestinationPath "$Xampp\php-8.5.12-1"
```

Skip extraction if that PHP folder already exists and was checked previously. Do not overwrite an existing PHP folder. A newer release has a different download address and checksum, both listed on php.net.

Set the paths once in each PowerShell window. Change the values to match your computer.

```powershell
$Xampp = 'C:\xampp'
$PhpFolder = 'C:\xampp\php-8.5.12-1'
$Php = Join-Path $PhpFolder 'php.exe'
$Composer = Join-Path $Xampp 'composer\composer.phar'
$Project = 'C:\path\to\jeee-unical'
$Apache = Join-Path $Xampp 'apache\bin\httpd.exe'
$Mysql = Join-Path $Xampp 'mysql\bin\mysql.exe'
```

`$Project` must be the folder that contains `artisan`. Replace `C:\path\to\jeee-unical` with your real path, for example `C:\Users\YourName\Desktop\jeee-unical`. To use the folder you are already in, run `$Project = (Get-Location).Path` instead.

Check the selected PHP, not the old PHP that another program may have installed.

```powershell
& $Php -v
```

For the version used here, the output must show PHP 8.5.12, a build date of October 26, 2026, and `ZTS`. The ZIP filename identifies the corrected Windows build as 8.5.12-1. `php -v` itself does not print the `-1` suffix.

### 3. Set up php.ini

The PHP zip does not contain `php.ini`. It only has `php.ini-development` and `php.ini-production`. Without `php.ini`, PHP loads no extensions, and Composer stops with "The openssl extension is required". Create `php.ini` from the template and turn on the extensions.

```powershell
$Ini = Join-Path $PhpFolder 'php.ini'
if (-not (Test-Path $Ini)) { Copy-Item "$PhpFolder\php.ini-development" $Ini }
$phpDir = $PhpFolder.Replace('\', '/')
$xamppDir = $Xampp.Replace('\', '/')
$c = Get-Content $Ini -Raw
$c = $c -replace ';\s*extension_dir\s*=\s*"ext"', "extension_dir=`"$phpDir/ext`""
foreach ($e in 'curl','exif','fileinfo','gd','intl','mbstring','mysqli','openssl','pdo_mysql','pdo_pgsql','pdo_sqlite','zip') {
    $c = $c -replace "(?m)^;\s*extension=$e\s*$", "extension=$e"
}
Set-Content $Ini $c -Encoding ASCII
```

Remove repeated `extension=` lines. A repeated line causes a "Module is already loaded" warning.

```powershell
$seen = @{}
$out = foreach ($l in (Get-Content $Ini)) {
    if ($l -match '^extension=(\w+)\s*$') {
        if ($seen[$Matches[1]]) { continue }
        $seen[$Matches[1]] = $true
    }
    $l
}
Set-Content $Ini $out -Encoding ASCII
```

Add the project settings and create the session folder.

```powershell
Add-Content $Ini @"

memory_limit=512M
max_execution_time=120
post_max_size=230M
upload_max_filesize=210M
date.timezone=Africa/Lagos
log_errors=On
error_reporting=E_ALL
session.save_path="$xamppDir/tmp"
session.upload_progress.enabled=0
opcache.enable=0
opcache.enable_cli=0
curl.cainfo="$xamppDir/apache/bin/curl-ca-bundle.crt"
openssl.cafile="$xamppDir/apache/bin/curl-ca-bundle.crt"
"@
New-Item -ItemType Directory -Path "$Xampp\tmp" -Force
```

Later lines override earlier ones in `php.ini`, so these values replace the template's. Upload progress must stay off. OPcache is disabled locally because it caused a Windows startup error when running two copies. The online PHP settings are in `docker/php/zz-laravel.ini` and enable OPcache.

Verify the settings and loaded PHP features.

```powershell
& $Php -c $Ini --ini
& $Php -c $Ini -d display_errors=1 -d display_startup_errors=1 -m
& $Php -c $Ini --ri session
```

`Loaded Configuration File` must show your `php.ini`. If it shows `(none)`, the file was not created. The module list must include `openssl`, `curl`, `mbstring`, `pdo_mysql`, `gd` and `zip`. If a "Unable to load dynamic library" message appears, `extension_dir` is wrong. The last command must show `session.upload_progress.enabled => Off`. Do not go to the next step until all of this is correct.

### 4. Install Composer

Download the tested Composer version and check the file.

```powershell
New-Item -ItemType Directory -Path "$Xampp\composer" -Force
curl.exe --fail --location --output $Composer 'https://getcomposer.org/download/2.11.2/composer.phar'
if ($LASTEXITCODE -ne 0) { throw 'Composer download failed' }
$Expected = 'dd158c0afd41d15fac89e1b4e47b5d42911fe316c3687cd5edac98319b25d028'
if ((Get-FileHash $Composer -Algorithm SHA256).Hash -ne $Expected) { throw 'Composer download check failed' }
& $Php -c "$PhpFolder\php.ini" $Composer --version
```

Composer needs the `openssl` extension. If `& $Php -c "$PhpFolder\php.ini" -m` does not list `openssl`, go back to step 3.

If a download or installation fails, stop at that step. Do not use winget or bypass a failed file check.

## Set up the project

### 1. Create the working folders and .env

Use a copy of the project that includes `artisan`, `bootstrap/app.php`, `composer.json`, `composer.lock`, `app`, `config`, `database`, `routes`, `resources`, and `public`.

```powershell
Set-Location $Project
$Folders = @('bootstrap\cache', 'storage\app\public', 'storage\framework\cache\data', 'storage\framework\sessions', 'storage\framework\views', 'storage\logs', 'storage\local')
foreach ($Folder in $Folders) {
    New-Item -ItemType Directory -Path $Folder -Force
}
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
```

Never replace an existing `.env` without checking it first. It contains the key and password for that copy of the site.

### 2. Create the local database

Start **MySQL** in the XAMPP control panel. Keep it limited to this computer. In `$Xampp\mysql\bin\my.ini`, under `[mysqld]`, set the following and restart MySQL if you changed it.

```ini
bind-address=127.0.0.1
```

Generate a password for a new project database user.

```powershell
$Bytes = New-Object byte[] 24
[Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($Bytes)
$DbPassword = -join ($Bytes | ForEach-Object { $_.ToString('x2') })
$DbPassword
```

Save that value. These commands create `jetsi_local` and give its user access only to that database. They connect as `root` without a password, which is the default on a new XAMPP. If you set a root password, add `-p` to each command.

```powershell
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="CREATE DATABASE IF NOT EXISTS jetsi_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="CREATE USER IF NOT EXISTS 'jetsi_local'@'localhost' IDENTIFIED BY '$DbPassword'"
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="CREATE USER IF NOT EXISTS 'jetsi_local'@'127.0.0.1' IDENTIFIED BY '$DbPassword'"
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="GRANT ALL PRIVILEGES ON jetsi_local.* TO 'jetsi_local'@'localhost'"
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="GRANT ALL PRIVILEGES ON jetsi_local.* TO 'jetsi_local'@'127.0.0.1'"
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="ALTER USER 'jetsi_local'@'localhost' IDENTIFIED BY '$DbPassword'"
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="ALTER USER 'jetsi_local'@'127.0.0.1' IDENTIFIED BY '$DbPassword'"
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="FLUSH PRIVILEGES"
```

Confirm the above works:
```powershell
& $Mysql -h 127.0.0.1 -P 3306 -u root --execute="SHOW DATABASES; SELECT user,host FROM mysql.user WHERE user='jetsi_local';"
```

`CREATE USER IF NOT EXISTS` does not change the password of a user that already exists. The `ALTER USER` lines set the password in every case, so MySQL always has the password in `$DbPassword`. Test the login before going on.

```powershell
& $Mysql -h 127.0.0.1 -P 3306 -u jetsi_local "-p$DbPassword" --execute="SELECT 1"
```

It must print a small table with `1`. If it says access denied, run the commands above again in the same PowerShell window.

For an independent copy, use a different database name and username throughout these commands and `.env`.

### 3. Fill in .env

Open `.env` and set these values. Replace the password placeholder with the password from the database step. Leave the other entries from `.env.example` in place.

To write that password into `.env` without editing it by hand, run this in the same PowerShell window and project folder used for the database step.

```powershell
(Get-Content .env) -replace '^DB_PASSWORD=.*', "DB_PASSWORD=$DbPassword" | Set-Content .env -Encoding ASCII
```

The other values below still need to be set in `.env`.

```dotenv
APP_NAME=JETSI_APP
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8080
LOG_CHANNEL=single
LOG_LEVEL=debug
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jetsi_local
DB_USERNAME=jetsi_local
DB_PASSWORD=your-local-database-password
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=null
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=log
MAIL_MAILER=log
```

For an existing site, keep its `APP_KEY`. Only a new copy needs a new key. Local email is written to `storage/logs/laravel.log`, not delivered. Google login and payments need separate account settings described below.

### 4. Install packages and create the tables

Run these commands from the project folder. Each uses the selected PHP.

```powershell
& $Php -c "$PhpFolder\php.ini" $Composer install --prefer-dist --no-interaction
& $Php -c "$PhpFolder\php.ini" $Composer check-platform-reqs
& $Php -c "$PhpFolder\php.ini" artisan config:clear
```

For a new copy with an empty `APP_KEY`, generate it once.

```powershell
& $Php -c "$PhpFolder\php.ini" artisan key:generate
```

Then create the missing tables and add the journal settings.

```powershell
& $Php -c "$PhpFolder\php.ini" artisan migrate
& $Php -c "$PhpFolder\php.ini" artisan db:seed --class=SettingsSeeder
& $Php -c "$PhpFolder\php.ini" artisan migrate:status
```

The settings command can be repeated. It leaves existing settings in place. Do not run `migrate:fresh`, `migrate:refresh`, or `migrate:reset` on a database containing data you need.

If `public/storage` is missing, create the link used for uploaded files.

```powershell
& $Php -c "$PhpFolder\php.ini" artisan storage:link
```

If Windows refuses the link, run that command from an administrator PowerShell window. If it already exists, check its destination rather than overwriting it.

## Run locally with XAMPP Apache

The following Apache settings belong only to this project. They select the corrected PHP without replacing the PHP used by other projects.

### 1. Write the project server settings

In the PowerShell window where you set the paths, run this block. It writes `storage/local/apache-manual.conf`. The paths are filled in from your variables.

In the current dir run:
```powershell
$Project = (Get-Location).Path
New-Item -ItemType Directory -Path "$Project\storage\local" -Force
```
then do this:

```powershell
$ApacheRoot = (Join-Path $Xampp 'apache').Replace('\', '/')
$PhpRoot = $PhpFolder.Replace('\', '/')
$Public = (Join-Path $Project 'public').Replace('\', '/')
$Local = (Join-Path $Project 'storage\local').Replace('\', '/')
$ApacheConfig = Join-Path $Project 'storage\local\apache-manual.conf'
@"
ServerRoot "$ApacheRoot"
Listen 127.0.0.1:8080
ServerName localhost
LoadModule authz_core_module modules/mod_authz_core.so
LoadModule authz_host_module modules/mod_authz_host.so
LoadModule authz_user_module modules/mod_authz_user.so
LoadModule dir_module modules/mod_dir.so
LoadModule mime_module modules/mod_mime.so
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule log_config_module modules/mod_log_config.so
LoadModule alias_module modules/mod_alias.so
LoadFile "$PhpRoot/libcrypto-3-x64.dll"
LoadFile "$PhpRoot/libssl-3-x64.dll"
LoadFile "$PhpRoot/libssh2.dll"
LoadFile "$PhpRoot/php8ts.dll"
LoadFile "$PhpRoot/libpq.dll"
LoadFile "$PhpRoot/libsqlite3.dll"
LoadModule php_module "$PhpRoot/php8apache2_4.dll"
PHPIniDir "$PhpRoot"
php_admin_flag opcache.enable Off
DocumentRoot "$Public"
DirectoryIndex index.php index.html
TypesConfig conf/mime.types
PidFile "$Local/apache-manual.pid"
ErrorLog "$Local/apache-manual-error.log"
CustomLog "$Local/apache-manual-access.log" common
<FilesMatch "\.php$">
    SetHandler application/x-httpd-php
</FilesMatch>
<Directory />
    AllowOverride None
    Require all denied
</Directory>
<Directory "$Public">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require local
    <FilesMatch "^(?!index\.php$).*\.(php|phtml|phar)$">
        Require all denied
    </FilesMatch>
</Directory>
"@ | Set-Content -LiteralPath $ApacheConfig -Encoding ASCII
```

### 2. Start and stop

Check the settings, then start Apache in the same window.

```powershell
& $Apache -t -f $ApacheConfig
& $Apache -f $ApacheConfig
```

Proceed only if the first command says `Syntax OK`. Leave this window open. Open **http://127.0.0.1:8080**. Press **Ctrl+C** in the Apache window to stop this server. MySQL can stay running.

If port 8080 is already in use, keep the existing server running and use a free port such as 8082. Change both `Listen` in the settings and `APP_URL` in `.env`. Do not stop another project's server.

For later starts, set the path variables again, start MySQL in the XAMPP control panel, set `$ApacheConfig` to the saved file, and run the two Apache commands. You do not need to reinstall packages or recreate the database.

Restart Apache after changing `php.ini`. After changing `.env`, run `artisan config:clear` with the selected PHP.

### 3. Check the site and create an admin

Open `http://127.0.0.1:8080/_local/runtime`. It must show the selected PHP 8.5 version, the correct `php.ini`, and `upload_progress` set to `0`. This page is disabled online.

Register your own account on the website. To give that account admin access, run this from another PowerShell window with the same paths.

```powershell
Set-Location $Project
& $Php -c "$PhpFolder\php.ini" artisan journal:admin you@example.com
```

Replace the email with the one you registered. Log in and open `/admin`. Add your actual journal issues and articles there.

Check registration, login, logout, profile, account subscriptions, admin lists, uploading a manuscript, creating an issue and article, search, and article downloads. A new database has no journal issues. An article download requires the account to have a subscription for that issue.

## Copy to another computer

Copy the source and `composer.lock`. Keep `public/vendor` and `resources/views/vendor`. Do not copy the root `vendor`, `node_modules`, `.env`, `bootstrap/cache/*.php`, `public/storage`, `storage/local`, `storage/basset`, or files in `storage/framework` and `storage/logs`.

The remote-control file `public/js/app.php` from the original project has been removed. Do not restore it from an older copy. Apache also blocks that filename.

On the other computer, follow the installation and setup sections using its own paths. Keep the PHP version and `composer.lock` the same. Older PHP elsewhere on that computer does not affect commands using `$Php`.

To move existing local content, export its database using phpMyAdmin or the command below. Stop writes to the site while copying the database and uploaded files.

```powershell
$Dump = Join-Path $Xampp 'mysql\bin\mysqldump.exe'
& $Dump -h 127.0.0.1 -P 3306 -u jetsi_local -p --single-transaction --result-file="$Project\local-backup.sql" jetsi_local
```

Copy `storage/app/public` separately. The database backup does not contain these files. Import the backup only into an empty destination database.

```powershell
$Backup = 'C:/path/to/local-backup.sql'
& $Mysql -h 127.0.0.1 -P 3306 -u jetsi_local -p --database=jetsi_local --execute="SOURCE $Backup"
```

Use a backup path without spaces for `SOURCE`. Keep backups outside the repository. Then run `artisan migrate`, the settings seed, and `artisan storage:link` if the link is missing. Keep the existing `APP_KEY` when moving the same site.

## Deploy online with Supabase and Render

Supabase holds the Postgres database. Render runs Apache and PHP from the project's `Dockerfile`. XAMPP is only for local use.

### 1. Prepare the source

Put the source in a private GitHub, GitLab, or Bitbucket repository. Include `Dockerfile`, `docker`, `render.yaml`, `composer.json`, and `composer.lock`. Follow the exclusions in the copying section. Never upload `.env`, database backups, passwords, or uploaded manuscripts.

The image installs PHP 8.5.12 and Composer 2.11.2. `docker/php/zz-laravel.ini` sets `session.upload_progress.enabled=0`.

### 2. Create the Supabase database

Create a Supabase project in a region near the Render service. Save the database password. In Supabase, select **Connect > Session pooler** and copy the host, database, and username exactly as shown.

Use port **5432**. The shared pooler username normally looks like `postgres.project-reference`. Do not use the transaction pooler on port 6543. A Supabase API key is not the database password.

These steps create an empty online database. Do not import a MySQL backup directly into Postgres. Moving existing MySQL content needs a separate data transfer, with backups and checks before switching users to the new site.

### 3. Prepare the online settings

Generate a key locally without changing the local `.env`.

```powershell
& $Php -c "$PhpFolder\php.ini" artisan key:generate --show
```

Keep the complete value, including `base64:`. For a new online site, set the following in Render's **Environment** page. Replace the key, URL, and database placeholders.

```dotenv
APP_NAME=JETSI_APP
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
APP_URL=https://your-service.onrender.com
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=pgsql
DB_HOST=your-session-pooler-host
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.your-project-reference
DB_PASSWORD=your-supabase-database-password
DB_SSLMODE=require
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=null
CACHE_STORE=file
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=log
MAIL_MAILER=log
RUN_MIGRATIONS=true
CACHE_CONFIG=true
```

Keep `APP_KEY` unchanged after launch. Changing it ends existing login sessions. For a transferred site, preserve the original key.

### 4. Choose how to create the Render service

**Option A: Blueprint**

Select **New > Blueprint**, connect the repository, and use `render.yaml`. Review the region and plan, then enter the settings above. Set `RUN_MIGRATIONS=true` for an empty database. The file defaults to `false` to avoid running table changes accidentally.

**Option B: Manual web service**

Select **New > Web Service**, connect the repository, and enter these settings.

| Setting | Value |
| --- | --- |
| Language | Docker |
| Root directory | Folder containing `Dockerfile`, or blank if it is the repository root |
| Dockerfile path | `./Dockerfile` relative to that folder |
| Docker command | Leave blank |
| Instance | Starter or higher |
| Region | Near the Supabase project |
| Health check | `/robots.txt` |
| Disk mount path | `/var/www/html/storage/app/public` |
| Disk size | 5 GB, or enough for your uploaded files |

Add the environment values above and deploy. Both options use the same image and startup commands.

A paid service is needed for the upload disk. Without it, uploaded files disappear after a restart or deployment. A disk-backed service runs one instance and has a short interruption during deployments.

### 5. Finish the first deployment

Watch the build and startup logs. `docker/start.sh` creates the working folders, links uploaded files, registers packages, runs pending table changes when `RUN_MIGRATIONS=true`, saves the settings cache, and starts Apache on Render's port.

If the build or table creation fails, stop and fix the reported error. Do not bypass PHP checks or downgrade PHP. The `/robots.txt` health check only proves that Apache responds, not that the database works.

Open **Shell** for the Render service and run these commands directly.

```sh
php -v
php --ri session
php artisan migrate:status
php artisan db:seed --class=SettingsSeeder --force
php artisan basset:cache
```

Check that PHP is at least 8.5.12, upload progress is off, all table changes are applied, and the admin page files downloaded successfully. Set `RUN_MIGRATIONS=false` after the first successful setup.

In Supabase's Table Editor, turn on **RLS** for every table created by the app. Leave public access rules empty. This blocks access through Supabase's public API. The private `postgres` server connection still works. Repeat this for new tables added later.

Register your account on the online site, then grant admin access in Render Shell.

```sh
php artisan journal:admin you@example.com
```

Check the same pages and uploads as locally. Restart the service and confirm the uploaded files remain. Do not move users to the online site until these checks pass.

## Deploy on another Docker host

Use this option only on a Linux server with Docker Engine installed, a domain pointing to the server, and ports 80 and 443 available. Install Docker from its [official Linux instructions](https://docs.docker.com/engine/install/) if needed. Do not expose the local XAMPP server to the internet.

Copy the source to `/srv/jetsi`. Create `/srv/jetsi/.env.production` with the online values above, using your domain for `APP_URL`. Keep this file outside version control and restrict access to it.

```sh
cd /srv/jetsi
chmod 600 .env.production
docker build -t jetsi:php85 .
docker network create jetsi-network
docker volume create jetsi-uploads
docker volume create jetsi-caddy-data
docker volume create jetsi-caddy-config
```

Create `/srv/jetsi/Caddyfile` with the following. Replace the example domain with yours. Caddy gives the site HTTPS and forwards requests to the app.

```caddyfile
journal.example.com {
    reverse_proxy jetsi:10000
}
```

Start the app and HTTPS server.

```sh
docker run -d --name jetsi --restart unless-stopped --network jetsi-network --env-file .env.production --mount type=volume,source=jetsi-uploads,target=/var/www/html/storage/app/public jetsi:php85
docker run -d --name jetsi-https --restart unless-stopped --network jetsi-network -p 80:80 -p 443:443 --mount type=bind,source=/srv/jetsi/Caddyfile,target=/etc/caddy/Caddyfile,readonly --mount type=volume,source=jetsi-caddy-data,target=/data --mount type=volume,source=jetsi-caddy-config,target=/config caddy:2
docker logs jetsi
docker logs jetsi-https
```

Complete the same first-deployment checks using `docker exec` instead of Render Shell.

```sh
docker exec jetsi php -v
docker exec jetsi php --ri session
docker exec jetsi php artisan migrate:status
docker exec jetsi php artisan db:seed --class=SettingsSeeder --force
docker exec jetsi php artisan basset:cache
docker exec jetsi php artisan journal:admin you@example.com
```

Register the account before the last command. Apply the Supabase RLS settings described above. After setup, set `RUN_MIGRATIONS=false` in `.env.production`. The new value takes effect when you recreate the app container.

For a later update, back up the database and upload volume first. Build the new image, then replace only the app container. The named upload volume is retained.

```sh
cd /srv/jetsi
docker build -t jetsi:php85 .
docker stop jetsi
docker rm jetsi
docker run -d --name jetsi --restart unless-stopped --network jetsi-network --env-file .env.production --mount type=volume,source=jetsi-uploads,target=/var/www/html/storage/app/public jetsi:php85
```

Do not remove the upload or Caddy volumes. They hold files that must survive an update.

## Email, Google login, and payments

These services need your own accounts. They were not tested with live credentials.

For delivered email, set the following in Render or the online environment file. Use the sender address approved by your email provider.

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_FROM_ADDRESS=journal@example.com
MAIL_FROM_NAME="JETSI"
```

Use `MAIL_SCHEME=smtps` if your provider requires implicit TLS, normally on port 465. Port 587 normally starts with SMTP and upgrades to TLS. The old `MAIL_ENCRYPTION` entry is not the connection setting used by the current SMTP configuration. Test registration email and password reset delivery before launch.

For Google login, create a Google Cloud web application login client. Add this allowed return address, replacing the domain.

```text
https://journal.example.com/auth/google/verify
```

Set `GOOGLE_OAUTH_ID` and `GOOGLE_OAUTH_SECRET` on the server. The return address must match `APP_URL`. Use a separate client for local testing if needed.

For Paystack, set `PAYSTACK_PUBLIC` and `PAYSTACK_SECRET` using test keys first. Test a payment and the resulting subscription before replacing them with live keys. Keep the secret key off browser code and out of the repository.

For a custom domain on Render, add it under **Settings > Custom Domains** and follow the DNS instructions shown. Then update `APP_URL` and Google's allowed return address. Apply environment changes through a Render deployment or by recreating the Docker app container.

## Backups and updates

Back up the database and `storage/app/public` separately. Render disk snapshots are not a replacement for a database backup. Check Supabase's backup availability for your plan. On another Docker host, back up the `jetsi-uploads` volume too.

File-based login sessions are lost during an online restart or deployment. This setup does not use a separate queue worker or scheduled task.

After an update that adds tables, run `php artisan migrate --force` on the server and enable RLS for the new tables. Never use `migrate:fresh`, `migrate:refresh`, or `migrate:reset` on the live database.

Render can restore a previous website deployment. This does not reverse database changes. On another Docker host, keep the previous image so it can be started again with the same upload volume.

Check [Backpack's license terms](https://backpackforlaravel.com/license) before public or paid use. No paid add-on or license is included in this setup.

## When something fails

| Problem | Check |
| --- | --- |
| PHP starts with a missing DLL message | Install the x64 Windows runtime and use the x64 Thread Safe PHP build |
| Composer says PHP is too old | Run it through `$Php`, not the computer's default `php` |
| Composer says the openssl extension is required | `php.ini` is missing or does not load openssl. Redo step 3 and check `-m` |
| `Loaded Configuration File: (none)` | `$PhpFolder\php.ini` does not exist. The zip does not include it |
| `Module "mysqli" is already loaded` | A repeated `extension=` line in `php.ini`. Run the duplicate-removal block in step 3 |
| A PHP feature is missing | Enable it in the selected `php.ini`, then restart Apache |
| `Could not find a part of the path` for `storage\local\apache-manual.conf` | `$Project` points to a folder that does not exist. Set it to the folder that contains `artisan`, then run the block again |
| Port 8080 is already in use | Stop only your own server or use another local port |
| Database connection fails | Check MySQL is running and `.env` matches the database name, user, and password |
| `Access denied for user 'jetsi_local'` | The password in `.env` is not the one MySQL has. Run the `ALTER USER` commands in the database step, write the same password to `.env`, then run `artisan config:clear` |
| Changing `.env` has no effect | Run `artisan config:clear` locally, or redeploy online |
| An upload link is broken | Check `public/storage` points to this copy's `storage/app/public` |
| Admin access is denied | Register first, then run `artisan journal:admin` with that email |
| A local page fails | Read `storage/logs/laravel.log` and `storage/local/apache-manual-error.log` |
| An online page fails | Read Render's logs or `docker logs jetsi`, then check the database settings |

Stop on a failed installation or command. Keep the error message and the file or program name. Do not erase existing data to make setup pass.
