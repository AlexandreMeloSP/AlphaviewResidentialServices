#!/bin/bash
set -e

echo "=== Alphaview Serviços Residenciais - Iniciando deploy ==="

echo "[1/6] Instalando dependências PHP (production)..."
composer install --optimize-autoloader --no-dev --no-interaction

echo "[2/6] Rodando migrations..."
php artisan migrate --force

echo "[3/6] Executando AdminSeeder (se SEED_ADMIN=true)..."
if [ "${SEED_ADMIN:-false}" = "true" ]; then
    php artisan db:seed --class=AdminSeeder --force || true
else
    echo "  → AdminSeeder ignorado (SEED_ADMIN não é 'true')"
fi

echo "[4/6] Criando symlink do storage..."
php artisan storage:link --force || true

echo "[5/6] Limpando e cacheando configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[6/6] Verificando healthcheck..."
php artisan about --only=environment 2>/dev/null || true

echo "=== Deploy concluído com sucesso ==="

exec php-fpm
