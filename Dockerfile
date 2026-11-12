#
# JETSI on Render with Supabase Postgres.
#
# PHP 8.5.12 matches the tested local runtime.
FROM php:8.5.12-apache-bookworm

ENV APP_ENV=production \
    APP_DEBUG=false \
    PORT=10000 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Install the PHP features used by the site.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev libpq-dev libzip-dev libonig-dev libicu-dev; \
    docker-php-ext-configure gd \
        --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_pgsql gd exif bcmath zip mbstring intl; \
    a2enmod rewrite headers; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# Match the Composer version used locally.
COPY --from=composer:2.11.2 /usr/bin/composer /usr/bin/composer

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

# Optimised autoloader. Scripts are skipped on purpose: package discovery runs at
# container start (docker/start.sh) so the build never boots the app or needs a DB.
RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
                storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache \
    && cp docker/start.sh /usr/local/bin/start.sh \
    && cp docker/db-check.php /usr/local/bin/db-check.php \
    && sed -i 's/\r$//' /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 10000

# start.sh finishes with `exec apache2-foreground`, so Apache is PID 1 and receives
# Render's stop signal (the base image sets STOPSIGNAL SIGWINCH = graceful stop).
CMD ["start.sh"]
