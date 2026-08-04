#!/usr/bin/env sh
set -eu

echo "[railway:init] Starting Laravel pre-deploy tasks"
echo "[railway:init] PHP version: $(php -r 'echo PHP_VERSION;')"
echo "[railway:init] APP_ENV: ${APP_ENV:-unset}"

if [ -z "${APP_KEY:-}" ]; then
  echo "[railway:init] ERROR: APP_KEY is missing. Set APP_KEY in the Railway backend service variables."
  exit 1
fi

if [ "${DB_CONNECTION:-}" = "mysql" ] && [ -z "${DB_URL:-}" ] && [ -z "${MYSQL_URL:-}" ] && [ -z "${DB_HOST:-}" ]; then
  echo "[railway:init] ERROR: MySQL is selected but no DB_URL, MYSQL_URL, or DB_HOST is configured."
  echo "[railway:init] Set DB_URL to the Railway MySQL reference variable, for example: \${{MySQL.MYSQL_URL}}"
  exit 1
fi

echo "[railway:init] Clearing file-based Laravel caches"
php artisan config:clear
php artisan event:clear
php artisan route:clear
php artisan view:clear

if [ "${RAILWAY_SKIP_MIGRATIONS:-false}" = "true" ]; then
  echo "[railway:init] WARNING: RAILWAY_SKIP_MIGRATIONS=true, skipping php artisan migrate --force"
else
  echo "[railway:init] Running database migrations"
  php artisan migrate --force --no-interaction
fi

echo "[railway:init] Rebuilding Laravel caches"
php artisan config:cache
php artisan event:cache
php artisan view:cache

echo "[railway:init] Pre-deploy tasks completed"
