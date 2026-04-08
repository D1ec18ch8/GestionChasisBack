#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
  cp .env.example .env || true
fi

upsert_env() {
  key="$1"
  value="$2"

  if [ -z "$key" ] || [ ! -f .env ]; then
    return
  fi

  if grep -Eq "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${value}|" .env
  else
    printf '\n%s=%s\n' "$key" "$value" >> .env
  fi
}

# Keep the runtime .env aligned with Docker env vars used by the API.
upsert_env "DB_CONNECTION" "${DB_CONNECTION:-mysql}"
upsert_env "DB_HOST" "${DB_HOST:-db}"
upsert_env "DB_PORT" "${DB_PORT:-3306}"
upsert_env "DB_DATABASE" "${DB_DATABASE:-proyecto-GestionChacis}"
upsert_env "DB_USERNAME" "${DB_USERNAME:-root}"
upsert_env "DB_PASSWORD" "${DB_PASSWORD:-}"
upsert_env "APP_TIMEZONE" "${APP_TIMEZONE:-America/Costa_Rica}"
upsert_env "JWT_TTL" "${JWT_TTL:-60}"
upsert_env "SESSION_DRIVER" "${SESSION_DRIVER:-file}"
upsert_env "CACHE_STORE" "${CACHE_STORE:-file}"

if [ -n "${JWT_SECRET:-}" ]; then
  upsert_env "JWT_SECRET" "$JWT_SECRET"
fi

rm -f bootstrap/cache/*.php || true

if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force || true
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force || true
fi

exec "$@"
