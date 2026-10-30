#
# Laravel 5.5 (jeee_unical) on Render, database: Supabase Postgres (pdo_pgsql).
#
# PHP 7.3 is used deliberately: composer.lock pins Laravel 5.5.18 / Symfony 3.3 /
# doctrine/dbal 2.6, which do not run on PHP 8, and the existing cPanel host
# runs ea-php73. php:7.3 is end-of-life upstream; see RENDER_DEPLOYMENT.md
# ("Known limitations") for the upgrade path.
FROM php:7.3.33-apache-buster

ENV APP_ENV=production \
    APP_DEBUG=false \
    PORT=10000 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Debian buster is archived; point apt at archive.debian.org so builds keep working.
RUN set -eux; \
    sed -i -e 's|deb.debian.org|archive.debian.org|g' \
           -e 's|security.debian.org|archive.debian.org|g' \
           -e '/buster-updates/d' /etc/apt/sources.list; \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99-archive; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev libpq-dev; \
    docker-php-ext-configure gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
        --with-png-dir=/usr/include/; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_pgsql gd exif bcmath opcache; \
    a2enmod rewrite headers; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# Composer 2.2 LTS is the last line that supports PHP 7.3.
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Apache: serve public/ on Render's $PORT; PHP: production settings.
COPY docker/apache/ports.conf /etc/apache2/ports.conf
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/servername.conf /etc/apache2/conf-available/servername.conf
COPY docker/php/zz-laravel.ini /usr/local/etc/php/conf.d/zz-laravel.ini
RUN a2enconf servername \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Dependencies first (cached unless composer.json/composer.lock change).
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader \
    && composer clear-cache

# Application source (see .dockerignore for what is left out).
COPY . .

# Laravel 5.5.18's PackageManifest expects the Composer 1 installed.json (a flat list);
# Composer 2 writes {"packages": [...]}, which crashes `package:discover`. Flatten it below.
# Optimised autoloader. Scripts are skipped on purpose: package discovery runs at
# container start (docker/start.sh) so the build never boots the app or needs a DB.
RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
                storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache \
    && php -r '$f="vendor/composer/installed.json"; $j=json_decode(file_get_contents($f),true); if(isset($j["packages"])){file_put_contents($f,json_encode($j["packages"]));}' \
    && cp docker/start.sh /usr/local/bin/start.sh \
    && cp docker/db-check.php /usr/local/bin/db-check.php \
    && sed -i 's/\r$//' /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Backpack admin assets (AdminLTE, Bootstrap, DataTables, select2, pnotify, ...).
# Backpack only auto-publishes these when APP_ENV=local, so in production the /admin
# pages had no CSS/JS at all. Copy them straight out of vendor/ (same targets that
# `php artisan vendor:publish --tag=public|adminlte` would write), no app boot needed.
RUN set -eux; \
    mkdir -p public/vendor/adminlte public/vendor/backpack; \
    cp -r vendor/almasaeed2010/adminlte/bootstrap vendor/almasaeed2010/adminlte/dist vendor/almasaeed2010/adminlte/plugins public/vendor/adminlte/; \
    cp -r vendor/backpack/base/src/public/. public/vendor/backpack/; \
    cp -r vendor/backpack/crud/src/public/. public/vendor/backpack/; \
    chown -R www-data:www-data public/vendor; \
    test -f public/vendor/adminlte/dist/css/AdminLTE.min.css; \
    test -f public/vendor/backpack/backpack.base.css

EXPOSE 10000

# start.sh finishes with `exec apache2-foreground`, so Apache is PID 1 and receives
# Render's stop signal (the base image sets STOPSIGNAL SIGWINCH = graceful stop).
CMD ["start.sh"]