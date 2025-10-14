#!/usr/bin/env bash
set -e

# تأكد من صلاحيات التخزين
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R a+rw storage bootstrap/cache

# احذف أي cache قديمة أولاً
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# نفّذ المهاجرات (إذا فشلت لا يوقف الحاوية بالكامل)
php artisan migrate --force || true

# الآن أنشئ الكاش باستخدام متغيرات البيئة الحالية (runtime)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ابدأ frankenphp باستخدام المنفذ الذي تزوده Railway
exec frankenphp -S 0.0.0.0:$PORT public/index.php
