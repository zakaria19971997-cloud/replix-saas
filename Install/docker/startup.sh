#!/bin/bash

echo "==> Copying nginx config..."
cp /var/www/html/docker/nginx.conf /etc/nginx/sites-available/default

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting services..."
exec /usr/bin/supervisord -c /var/www/html/docker/supervisord.conf
