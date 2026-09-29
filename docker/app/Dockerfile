FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && pecl channel-update pecl.php.net && pecl install redis-6.1.0 && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN usermod -o -u 1000 www-data && groupmod -o -g 1000 www-data

WORKDIR /var/www/html

# PHP deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# JS deps + Vite build
COPY package.json package-lock.json vite.config.js tsconfig.json tsconfig.node.json ./
COPY resources/ ./resources/
RUN npm ci --ignore-scripts && node node_modules/vite/bin/vite.js build

# App code
COPY . .

# Static assets (symlink not supported in Docker context)
RUN cp -r assets public/assets && ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

# Autoloader
RUN composer dump-autoload --optimize --no-dev

# Directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/app/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

USER www-data

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
