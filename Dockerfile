# ==========================================================
# 🧱 المرحلة 1: تثبيت PHP والإضافات
# ==========================================================
FROM php:8.2-fpm-bullseye AS base

# تثبيت المكتبات المطلوبة وPHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl gd zip pdo pdo_mysql

WORKDIR /app

# ==========================================================
# 📦 المرحلة 2: تثبيت Composer dependencies
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

# إنشاء مجلدات التخزين وضبط الصلاحيات
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# نسخ ملف entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# المنفذ الافتراضي في Railway
EXPOSE 8080

# بدء الحاوية باستخدام entrypoint
ENTRYPOINT ["/entrypoint.sh"]
