#!/bin/sh
set -eu

cd /var/www/html

mkdir -p \
  storage/app/public \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ ! -L public/storage ]; then
  php artisan storage:link --no-interaction || true
fi

if [ "${WAIT_FOR_DATABASE:-true}" = "true" ] && [ "${DB_CONNECTION:-}" != "sqlite" ]; then
  retries="${DB_WAIT_RETRIES:-30}"
  sleep_seconds="${DB_WAIT_SLEEP_SECONDS:-2}"
  attempt=1

  until php artisan db:show --no-interaction > /dev/null 2>&1; do
    if [ "$attempt" -ge "$retries" ]; then
      echo "Database is unavailable after ${retries} attempts."
      php artisan db:show --no-interaction
      exit 1
    fi

    echo "Waiting for database connection (${attempt}/${retries})..."
    attempt=$((attempt + 1))
    sleep "$sleep_seconds"
  done
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force
fi

if [ "${LARAVEL_OPTIMIZE:-true}" = "true" ]; then
  php artisan optimize:clear
  php artisan config:cache
  php artisan view:cache
fi

exec /usr/bin/supervisord -c /etc/supervisor.d/supervisord.ini
