# ==========================================================
# 🧱 المرحلة 1: تثبيت PHP والإضافات وComposer
# ==========================================================
FROM php:8.2-fpm-bullseye AS base

# تثبيت المكتبات المطلوبة وPHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl gd zip pdo pdo_mysql

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# نسخ ملفات Composer
COPY composer.json composer.lock ./

# تثبيت حزم PHP
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# ==========================================================
# 🚀 المرحلة 2: بناء نسخة الإنتاج
# ==========================================================
FROM php:8.2-fpm-bullseye AS production

WORKDIR /app

# تثبيت نفس الإضافات
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl gd zip pdo pdo_mysql

# نسخ vendor من مرحلة base
COPY --from=base /app/vendor ./vendor

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
