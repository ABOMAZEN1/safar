# ==========================================================
# 🧱 المرحلة 1: PHP + extension + Composer (لبناء vendor)
# ==========================================================
FROM php:8.2-fpm-bullseye AS base

# تثبيت مكتبات النظام و PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
  && docker-php-ext-install intl gd zip pdo pdo_mysql

# انسخ binary الخاص بـ composer من صورة composer الرسمية
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# انسخ ملفات composer فقط للحصول على layer قابل لإعادة الاستخدام
COPY composer.json composer.lock ./

# ثبت зависимости بدون تنفيذ السكربتات (لتفادي الحاجة لوجود artisan الآن)
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts

# ==========================================================
# 🚀 المرحلة 2: بناء نسخة الإنتاج النهائية
# ==========================================================
FROM php:8.2-fpm-bullseye AS production

WORKDIR /app

# ثبت نفس الإضافات في الصورة النهائية
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
  && docker-php-ext-install intl gd zip pdo pdo_mysql

# انسخ vendor من المرحلة السابقة
COPY --from=base /app/vendor ./vendor

# الآن انسخ بقية ملفات المشروع (بما في ذلك artisan)
COPY . .

# بعد نسخ المشروع، نعيد توليد الـ autoload ونشغّل package discovery
RUN composer dump-autoload --optimize || true
RUN php artisan package:discover --ansi || true

# إعداد المجلدات و الأذونات
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# انسخ entrypoint وشغّله
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]
