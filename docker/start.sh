#!/bin/sh
# Container entrypoint for Render. Safe by default: it never seeds, resets or
# drops anything. Migrations only run when RUN_MIGRATIONS=true.
set -eu

APP_ROOT="${APP_ROOT:-/var/www/html}"
cd "$APP_ROOT"

log() { echo "[start] $*"; }

PORT="${PORT:-10000}"
export PORT

if [ -z "${APP_KEY:-}" ]; then
    echo "[start] ERROR: APP_KEY is not set." >&2
    echo "[start] Generate one with:  echo \"base64:\$(openssl rand -base64 32)\"" >&2
    echo "[start] and add it in Render > Environment. Refusing to start without it." >&2
    exit 1
fi

# --- Writable directories (a mounted Render disk arrives root-owned) ---------
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
         storage/framework/views storage/logs bootstrap/cache

# --- public/storage -> storage/app/public (DownloadController serves from it) --
rm -rf public/storage
php artisan storage:link

# --- public/uploads (Backpack/elFinder file manager, TinyMCE images) ---------
# Render allows ONE disk per service, so keep these files on the same disk as
# storage/app/public by pointing public/uploads at storage/app/public/uploads.
mkdir -p storage/app/public/uploads
if [ -d public/uploads ] && [ ! -L public/uploads ]; then
    cp -an public/uploads/. storage/app/public/uploads/ 2>/dev/null || true
    rm -rf public/uploads
fi
ln -sfn ../storage/app/public/uploads public/uploads

# --- Package discovery (Backpack, Intervention, Socialite, ...) --------------
php artisan package:discover

# --- Optional, non-destructive migrations ------------------------------------
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    if [ "${DB_CONNECTION:-mysql}" = "mysql" ] || [ "${DB_CONNECTION:-mysql}" = "pgsql" ]; then
        log "Waiting for the database..."
        tries=0
        until php "${DB_CHECK_SCRIPT:-/usr/local/bin/db-check.php}"; do
            tries=$((tries + 1))
            if [ "$tries" -ge 30 ]; then
                echo "[start] ERROR: database unreachable after 30 attempts; not migrating." >&2
                exit 1
            fi
            sleep 2
        done
    fi
    log "Running migrations (php artisan migrate --force)..."
    php artisan migrate --force
else
    log "RUN_MIGRATIONS is not 'true'; skipping migrations."
fi

# --- Config cache (route:cache is intentionally NOT used: routes/api.php has closures)
php artisan config:clear >/dev/null 2>&1 || true
if [ "${CACHE_CONFIG:-true}" = "true" ]; then
    php artisan config:cache
fi

# --- Ownership last, so files created by the artisan calls above are writable by Apache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

log "Starting Apache on port ${PORT}"
exec apache2-foreground
