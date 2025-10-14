# =============== المرحلة الأولى: تثبيت الحزم ===============
FROM dunglas/frankenphp:php8.2.29-bookworm AS base

# تثبيت الإضافات المطلوبة للـ Laravel (intl, gd, zip, pdo_mysql)
RUN install-php-extensions intl gd zip pdo_mysql

WORKDIR /app

# =============== المرحلة الثانية: تثبيت الحزم عبر Composer ===============
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --optimize-autoloader

# =============== المرحلة الثالثة: بناء التطبيق النهائي ===============
FROM base AS final

WORKDIR /app

# نسخ المجلد vendor من المرحلة السابقة
COPY --from=vendor /app/vendor ./vendor

# نسخ باقي ملفات المشروع
COPY . .

# إنشاء المجلدات وضبط التصاريح
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# نسخ ملف التشغيل (إن وجد)
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh || true

# المنفذ الافتراضي في Railway
EXPOSE 8080
ENV SERVER_PORT=8080

# تشغيل الخادم
CMD ["frankenphp", "php-server", "--root", "public", "--port", "8080"]
