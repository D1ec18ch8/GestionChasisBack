#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
  cp .env.example .env || true
fi

if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force || true
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force || true
fi

exec "$@"
