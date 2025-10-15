# --------------------------------------------------
# 1️⃣ نستخدم صورة PHP مناسبة مع Composer و Node
# --------------------------------------------------
    FROM dunglas/frankenphp:php8.2-bookworm AS base

    # تثبيت الأدوات الضرورية + Composer
    RUN apt-get update && apt-get install -y \
        git unzip zip curl libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
        && docker-php-ext-install intl gd zip pdo pdo_mysql \
        && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    
    # --------------------------------------------------
    # 2️⃣ إعداد مجلد العمل داخل الحاوية
    # --------------------------------------------------
    WORKDIR /app
    
    # --------------------------------------------------
    # 3️⃣ نسخ ملفات Laravel وتثبيت Composer dependencies
    # --------------------------------------------------
    COPY . .
    RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress
    
    # --------------------------------------------------
    # 4️⃣ إعداد Node (لو عندك Vite أو Filament يحتاجه)
    # --------------------------------------------------
    FROM node:22 AS frontend
    WORKDIR /app
    COPY . .
    RUN npm ci && npm run build
    
    # --------------------------------------------------
    # 5️⃣ نقل الملفات النهائية إلى بيئة الإنتاج
    # --------------------------------------------------
    FROM base AS final
    WORKDIR /app
    COPY --from=frontend /app/public ./public
    COPY --from=frontend /app/resources ./resources
    COPY --from=base /app/vendor ./vendor
    COPY . .
    
    # --------------------------------------------------
    # 6️⃣ إعداد Laravel للتشغيل
    # --------------------------------------------------
    RUN php artisan config:cache && php artisan route:cache && php artisan view:cache
    
    # --------------------------------------------------
    # 7️⃣ تشغيل السيرفر
    # --------------------------------------------------
    EXPOSE 8080
    CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
    