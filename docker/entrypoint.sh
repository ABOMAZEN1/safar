#!/bin/sh

echo "[entrypoint] إعداد Laravel..."

# إنشاء key لو مش موجود
if [ -z "$(grep ^APP_KEY= .env | cut -d '=' -f2)" ]; then
    echo "🚀 Generating APP_KEY..."
    php artisan key:generate --force
fi

# تشغيل المايغريشنس
echo "📦 Running migrations..."
php artisan migrate --force

# تنظيف وتهيئة الكاش
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ كل شيء جاهز، تشغيل السيرفر..."
php artisan serve --host=0.0.0.0 --port=8080
