#!/bin/sh
set -e

echo "[entrypoint] ensuring folders and permissions..."
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R a+rw storage bootstrap/cache || true

echo "[entrypoint] clearing old caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# If APP_KEY is missing, generate a temporary one (recommended: set APP_KEY in Railway instead)
if [ -z "$APP_KEY" ]; then
  echo "[entrypoint] APP_KEY not found - generating temporary key"
  php artisan key:generate --force
fi

# Optionally cache config/routes/views now that env vars are present
echo "[entrypoint] caching config/route/view (optional)..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "[entrypoint] starting server..."
exec php artisan serve --host=0.0.0.0 --port=8080
