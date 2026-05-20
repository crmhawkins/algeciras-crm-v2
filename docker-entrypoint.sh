#!/usr/bin/env bash
set -e

cd /var/www/html

# Generate APP_KEY if not set
if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY not set — generating..."
  php artisan key:generate --force --no-interaction || true
fi

# Wait for DB
echo "Waiting for MySQL ${DB_HOST}:${DB_PORT}..."
for i in {1..60}; do
  if php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
    echo "MySQL is up."
    break
  fi
  echo "  attempt $i/60..."
  sleep 2
done

# Clear caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Migrate database (idempotent)
echo "Running migrations..."
php artisan migrate --force --no-interaction || echo "Migration failed - continuing anyway"

# Seed base data (idempotent via updateOrCreate / firstOrCreate)
echo "Running base seeders..."
php artisan db:seed --force --no-interaction || echo "Seeder failed - continuing anyway"

# Storage symlink
php artisan storage:link --force || true

# Cache for production
if [ "${APP_ENV:-local}" != "local" ]; then
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
fi

# Permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "Starting Apache..."
exec apache2-foreground
