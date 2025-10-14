# Stage 1: Build Composer dependencies
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --ignore-platform-req=ext-intl --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip \
    --no-dev --no-scripts --no-interaction --optimize-autoloader

# Stage 2: Final PHP image
FROM dunglas/frankenphp:php8.2.29-bookworm

# تثبيت الإضافات المطلوبة
RUN install-php-extensions intl gd zip pdo_mysql

WORKDIR /app

# نسخ ملفات المشروع و vendor
COPY --from=vendor /app/vendor ./vendor
COPY . .

# إعداد Laravel caches
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache \
 

# Expose PORT (Railway يستخدم متغير البيئة PORT)
EXPOSE 8080

# CMD بصيغة shell لتوسيع المتغير $PORT بشكل صحيح
CMD frankenphp -S 0.0.0.0:$PORT public/index.php
