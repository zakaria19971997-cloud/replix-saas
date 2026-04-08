#!/bin/bash

echo "==> Copying nginx config..."
cp /var/www/html/docker/nginx.conf /etc/nginx/sites-available/default

echo "==> Waiting for MySQL to be ready..."
for i in $(seq 1 30); do
    php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null && break
    echo "MySQL not ready yet ($i/30), waiting 3s..."
    sleep 3
done

echo "==> Discovering packages..."
php artisan package:discover --ansi 2>/dev/null || true

echo "==> Caching Laravel config..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Running migrations..."
php artisan migrate --force || true

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting services..."
exec /usr/bin/supervisord -c /var/www/html/docker/supervisord.conf
