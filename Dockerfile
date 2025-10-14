# ================================
#  Stage 1: Install Composer dependencies
# ================================
FROM dunglas/frankenphp:php8.2.29-bookworm AS vendor

# تثبيت الإضافات المطلوبة قبل تشغيل composer
RUN install-php-extensions intl gd zip pdo_mysql

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader

# ================================
#  Stage 2: Build final app image
# ================================
FROM dunglas/frankenphp:php8.2.29-bookworm

# تثبيت نفس الإضافات في الصورة النهائية
RUN install-php-extensions intl gd zip pdo_mysql

WORKDIR /app

# نسخ vendor من المرحلة السابقة
COPY --from=vendor /app/vendor ./vendor

# نسخ بقية ملفات المشروع
COPY . .

# إعداد صلاحيات Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# المنفذ
EXPOSE 8080
ENV SERVER_PORT=8080

# تشغيل التطبيق عبر FrankenPHP
CMD ["frankenphp", "run", "--config", "public"]
