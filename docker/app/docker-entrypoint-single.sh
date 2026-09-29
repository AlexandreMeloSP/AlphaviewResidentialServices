#!/bin/bash
set -e

mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

php artisan migrate --force --no-interaction 2>/dev/null

if [ "${SEED_ADMIN:-false}" = "true" ]; then
    php artisan db:seed --class=AdminSeeder --force 2>/dev/null || true
fi

if [ "${APP_ENV:-local}" != "production" ]; then
    php artisan key:generate --force --no-interaction 2>/dev/null || true
    php artisan db:seed --force 2>/dev/null || true
fi

php artisan storage:link --force 2>/dev/null || true
php artisan config:cache 2>/dev/null
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

exec "$@"
