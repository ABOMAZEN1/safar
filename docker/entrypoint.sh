#!/usr/bin/env bash
set -e

# مسار لوج بداية التشغيل
START_LOG=/tmp/startup.log
: > "$START_LOG"

echolog() {
  echo "[$(date +'%F %T')] $*" | tee -a "$START_LOG"
}

echolog "[entrypoint] start"

# تأكد من المجلدات والأذونات
echolog "[entrypoint] ensure folders & permissions"
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R a+rw storage bootstrap/cache || true

# مسح الكاشات (آمن)
echolog "[entrypoint] clearing caches"
php artisan config:clear 2>&1 | tee -a "$START_LOG" || true
php artisan cache:clear 2>&1 | tee -a "$START_LOG" || true
php artisan route:clear 2>&1 | tee -a "$START_LOG" || true
php artisan view:clear 2>&1 | tee -a "$START_LOG" || true

# اختبار متغيرات البيئة الأساسية
echolog "[entrypoint] env check"
php -r 'echo "DB_HOST=" . getenv("DB_HOST") . PHP_EOL; echo "DB_DATABASE=" . getenv("DB_DATABASE") . PHP_EOL; echo "DB_USERNAME=" . getenv("DB_USERNAME") . PHP_EOL;' 2>&1 | tee -a "$START_LOG" || true

# دالة لفحص توفر MySQL (PDO) مع عدد محاولات محدد
wait_for_db() {
  MAX_TRIES=20
  SLEEP=3
  i=0
  echolog "[entrypoint] waiting for DB (max $((MAX_TRIES * SLEEP))s)..."
  while [ $i -lt $MAX_TRIES ]; do
    if php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'OK'; } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
      echolog "[entrypoint] DB reachable"
      return 0
    fi
    i=$((i+1))
    echolog "[entrypoint] DB not ready, sleeping ${SLEEP}s (attempt $i/$MAX_TRIES)"
    sleep $SLEEP
  done
  echolog "[entrypoint] DB did not become ready after $((MAX_TRIES * SLEEP))s"
  return 1
}

# انتظر DB قبل تشغيل الميجريشن (إذا تعريف DB موجود)
if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then
  if wait_for_db ; then
    echolog "[entrypoint] running migrations (if any)..."
    php artisan migrate --force 2>&1 | tee -a "$START_LOG" || echolog "[entrypoint] migrate returned non-zero (continuing)..."
  else
    echolog "[entrypoint] skipping migrations because DB unreachable"
  fi
else
  echolog "[entrypoint] DB env not set, skipping DB wait/migrate"
fi

# إعادة توليد autoload و package discover بأمان (لو احتاج)
echolog "[entrypoint] composer dump-autoload & package discover (safe)"
composer dump-autoload --optimize 2>&1 | tee -a "$START_LOG" || true
php artisan package:discover --ansi 2>&1 | tee -a "$START_LOG" || true

# طباعة آخر الأسطر من Laravel log و startup log لكي تساعدنا بالـ debug فورًا
echolog "[entrypoint] last lines of storage/logs/laravel.log (if exists):"
if [ -f storage/logs/laravel.log ]; then
  tail -n 80 storage/logs/laravel.log 2>&1 | tee -a "$START_LOG"
else
  echolog "[entrypoint] no laravel.log found"
fi

echolog "[entrypoint] startup log preview:"
tail -n 200 "$START_LOG" || true

# اختيار السيرفر المناسب: frankenphp إن كان متوفر، وإلا php -S
if command -v frankenphp >/dev/null 2>&1; then
  echolog "[entrypoint] starting frankenphp on 0.0.0.0:8080"
  exec frankenphp php-server --root public --port 8080
else
  echolog "[entrypoint] starting built-in php server on 0.0.0.0:8080"
  # IMPORTANT: provide router script so that all dynamic routes (e.g., /livewire/livewire.js) are handled by Laravel
  exec php -S 0.0.0.0:8080 -t public public/index.php
fi
