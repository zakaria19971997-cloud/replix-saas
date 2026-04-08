#!/bin/bash
set -e

echo "==> Copying nginx config..."
cp /var/www/html/docker/nginx.conf /etc/nginx/sites-available/default

echo "==> Caching Laravel config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Creating storage symlink..."
php artisan storage:link || true

echo "==> Starting services..."
exec /usr/bin/supervisord -c /var/www/html/docker/supervisord.conf
