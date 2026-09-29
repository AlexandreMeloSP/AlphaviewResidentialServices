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
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=" .env || [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi

if [ "$1" = "php-fpm" ]; then
    echo "Waiting for MySQL..."
    RETRIES=30
    until php artisan db:show > /dev/null 2>&1 || [ $RETRIES -eq 0 ]; do
        echo "MySQL not ready yet, waiting... ($RETRIES)"
        RETRIES=$((RETRIES-1))
        sleep 2
    done

    if [ $RETRIES -eq 0 ]; then
        echo "MySQL did not respond in time, continuing anyway..."
    else
        echo "MySQL is ready!"
    fi

    php artisan migrate --force --no-interaction 2>/dev/null || true
    if [ "${APP_ENV:-local}" != "production" ]; then
        php artisan db:seed --force 2>/dev/null || true
    fi
    php artisan storage:link --force 2>/dev/null || true
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
fi

exec "$@"
