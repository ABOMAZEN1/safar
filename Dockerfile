# ================================
#  Stage 1: Install Composer dependencies
# ================================
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./

# تثبيت حزم Laravel بدون متطلبات الإضافات أثناء الـ build
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader

# ================================
#  Stage 2: Build final app image
# ================================
FROM dunglas/frankenphp:php8.2.29-bookworm

# تثبيت الإضافات اللازمة لـ Laravel
RUN install-php-extensions intl gd zip pdo_mysql

WORKDIR /app

# نسخ الملفات من مرحلة vendor
COPY --from=vendor /app/vendor ./vendor

# نسخ باقي ملفات المشروع
COPY . .

# تهيئة مجلدات Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# المنفذ الذي يستخدمه FrankenPHP
EXPOSE 8080
ENV SERVER_PORT=8080

# إيقاف أوامر الكاش أثناء build لأنها تحتاج قاعدة بيانات
# سيتم إنشاء الكاش تلقائيًا عند التشغيل

# تشغيل التطبيق عبر FrankenPHP (وليس artisan serve)
CMD ["frankenphp", "run", "--config", "public"]
