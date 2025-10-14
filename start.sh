#!/usr/bin/env bash
set -e

# --- إعداد المسارات وصلاحيات التخزين ---
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R a+rw storage bootstrap/cache

# --- لو عندك JSON لفايربيز في متغير بيئي (مثال: FIREBASE_JSON) نكتب الملف داخل الحاوية ---
# ضع في Railway متغيرًا اسمه FIREBASE_JSON يحتوي على محتوى firebase.json (مصغّر أو مضغوط)
if [ ! -z "$FIREBASE_JSON" ]; then
  mkdir -p public/firebase
  echo "$FIREBASE_JSON" > public/firebase/firebase.json
  export FIREBASE_CREDENTIALS="$(pwd)/public/firebase/firebase.json"
fi

# --- نظف أي cache سابقة (أمان) ---
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# --- نفّذ المهاجرات (إن فشلت فلن يكسر الحاوية بالكامل بسبب || true) ---
php artisan migrate --force || true

# --- الآن ابني cache باستخدام متغيرات البيئة الحقيقية (runtime) ---
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- شغّل السيرفر على المنفذ الذي تزوده Railway ---
exec frankenphp -S 0.0.0.0:$PORT public/index.php
