# ==========================================================
# 🧱 المرحلة 1: بناء الـ Composer dependencies
# ==========================================================
FROM php:8.2-fpm-bullseye AS build

# تثبيت الإضافات المطلوبة
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl gd zip pdo pdo_mysql

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# نسخ ملفات Laravel
COPY composer.json composer.lock ./

# تثبيت مكتبات PHP بدون سكريبتات post-install
RUN COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_DISCARD_CHANGES=1 \
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-progress

# نسخ كل المشروع
COPY . .

# ==========================================================
# 🏗️ المرحلة 2: بناء الـ Frontend (اختياري)
# ==========================================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci && npm run build

# ==========================================================
# 🚀 المرحلة 3: التشغيل الفعلي
# ==========================================================
FROM php:8.2-fpm-bullseye AS production

# تثبيت الإضافات
RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install intl gd zip pdo pdo_mysql

# نسخ Composer من المرحلة الأولى
COPY --from=build /usr/bin/composer /usr/bin/composer

WORKDIR /app

# نسخ الملفات من مرحلة البناء
COPY --from=build /app ./

# نسخ الـfrontend الجاهز (لو موجود)
COPY --from=frontend /app/public/build ./public/build

# إنشاء مجلدات Laravel اللازمة
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# ==========================================================
# 🧩 أوامر بدء التشغيل
# ==========================================================

# 1️⃣ توليد APP_KEY إذا مفقود
# 2️⃣ تشغيل الـmigrations (بدون توقف)
# 3️⃣ تشغيل السيرفر
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
