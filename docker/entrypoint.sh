#!/bin/bash
set -e

echo "[entrypoint] setting permissions..."
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R a+rw storage bootstrap/cache

echo "[entrypoint] clearing old caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "[entrypoint] running migrations..."
php artisan migrate --force

echo "[entrypoint] starting PHP built-in server..."
exec php -S 0.0.0.0:8080 -t public
