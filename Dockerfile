# --- Stage 1: Build Composer dependencies ---
    FROM composer:2 AS vendor

    WORKDIR /app
    COPY composer.json composer.lock ./
    RUN composer install --ignore-platform-req=ext-intl --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip --no-dev --no-scripts --no-interaction --optimize-autoloader
    
    # --- Stage 2: Build final PHP image ---
    FROM dunglas/frankenphp:php8.2.29-bookworm
    
    # تثبيت الإضافات اللازمة
    RUN install-php-extensions intl gd zip pdo_mysql
    
    WORKDIR /app
    
    # نسخ الملفات من مرحلة vendor
    COPY --from=vendor /app/vendor ./vendor
    COPY . .
    
    # إعداد Laravel
    RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
        && chmod -R a+rw storage bootstrap/cache \
 
    
    # المنفذ
    EXPOSE 8080
    
    # الأمر لتشغيل التطبيق
    CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
    