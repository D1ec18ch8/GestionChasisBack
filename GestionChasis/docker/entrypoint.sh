#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
  cp .env.example .env || true
fi

if [ -n "$APP_KEY" ]; then
  php artisan key:generate --force || true
fi

if [ -n "$DB_HOST" ]; then
  echo "Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
  until mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" --silent; do
    sleep 2
  done
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force || true
fi

exec "$@"
