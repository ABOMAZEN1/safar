# --------------------------------------------------
# 1️⃣ نستخدم صورة PHP مناسبة مع Composer و Node
# --------------------------------------------------
    FROM dunglas/frankenphp:php8.2-bookworm AS base

    # تثبيت الأدوات الضرورية
    RUN apt-get update && apt-get install -y \
        git unzip zip libzip-dev libpng-dev libicu-dev libonig-dev libxml2-dev \
        && docker-php-ext-install intl gd zip pdo pdo_mysql
    
    # --------------------------------------------------
    # 2️⃣ إعداد مجلد العمل داخل الحاوية
    # --------------------------------------------------
    WORKDIR /app
    
    # نسخ ملفات Laravel
    COPY . .
    
    # --------------------------------------------------
    # 3️⃣ تثبيت Composer dependencies
    # --------------------------------------------------
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
    CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
    