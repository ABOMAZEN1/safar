#!/bin/bash
set -e

echo "[entrypoint] ensuring folders and permissions..."
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R a+rw storage bootstrap/cache

echo "[entrypoint] clearing old caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# تأخّر قصير لإعطاء DB فرصة للظهور (optional, 3s)
sleep 3

# حاول تشغيل migrations (إن أردت). إذا كانت تسبب مشاكل يمكنك التعليق عنها مؤقتاً.
php artisan migrate --force || true

echo "[entrypoint] starting PHP built-in server..."
exec php -S 0.0.0.0:8080 -t public
