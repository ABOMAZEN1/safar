# ==========================================================
# 🧱 المرحلة 1: تثبيت مكتبات النظام و Composer
# ==========================================================
FROM php:8.2-fpm-bullseye AS base

# تثبيت الإضافات والمكتبات المطلوبة
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl gd zip pdo pdo_mysql

WORKDIR /app

# ==========================================================
# 📦 المرحلة 2: تثبيت الـ Composer dependencies
# ==========================================================
FROM composer:latest AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# ==========================================================
# 🚀 المرحلة 3: بناء نسخة الإنتاج
# ==========================================================
FROM base AS production

WORKDIR /app

# نسخ vendor من مرحلة Composer
COPY --from=vendor /app/vendor ./vendor

# نسخ باقي المشروع
COPY . .

# إنشاء مجلدات التخزين والصلاحيات
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# ==========================================================
# ⚙️ الأوامر النهائية عند التشغيل
# ==========================================================
# تنظيف الكاش وتشغيل السيرفر
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8080
