#!/bin/bash

echo "==> Generating .env from Railway environment variables..."
php -r "
\$envFile = '/var/www/html/.env';

// Preserve APP_INSTALLED=true from a previous run (survives container restarts)
\$alreadyInstalled = false;
if (file_exists(\$envFile)) {
    foreach (file(\$envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as \$line) {
        if (trim(\$line) === 'APP_INSTALLED=true') { \$alreadyInstalled = true; break; }
    }
}

\$keys = [
    'APP_NAME','APP_ENV','APP_KEY','APP_DEBUG','APP_URL','APP_TIMEZONE',
    'APP_LOCALE','APP_FALLBACK_LOCALE','APP_INSTALLED',
    'SITE_TITLE','SITE_DESCRIPTION','SITE_KEYWORDS',
    'THEMES_DIR','THEME_FRONTEND','DEMO_MODE',
    'LOG_CHANNEL','LOG_LEVEL','BCRYPT_ROUNDS',
    'DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD',
    'SESSION_DRIVER','SESSION_LIFETIME','SESSION_SECURE_COOKIE','SESSION_ENCRYPT',
    'CACHE_STORE','QUEUE_CONNECTION','BROADCAST_CONNECTION','FILESYSTEM_DISK',
    'WA_SERVER_URL','TRUSTED_PROXIES','FORCE_HTTPS',
    'PUSHER_APP_ID','PUSHER_APP_KEY','PUSHER_APP_SECRET','PUSHER_APP_CLUSTER',
    'VITE_APP_NAME',
];
\$lines = [];
foreach (\$keys as \$key) {
    \$val = getenv(\$key);
    if (\$key === 'APP_INSTALLED' && \$alreadyInstalled) { \$val = 'true'; }
    if (\$val !== false && \$val !== '') {
        if (preg_match('/[\s\#\"]/', \$val)) {
            \$val = '\"' . str_replace(['\\\\', '\"'], ['\\\\\\\\', '\\\\\"'], \$val) . '\"';
        }
        \$lines[] = \$key . '=' . \$val;
    }
}
file_put_contents(\$envFile, implode(\"\n\", \$lines) . \"\n\");
echo count(\$lines) . ' variables written to .env' . PHP_EOL;
"

echo "==> Ensure installer Blade cache dir is writable..."
mkdir -p /var/www/html/installer/cache
chown www-data:www-data /var/www/html/installer/cache

echo "==> Copying nginx config..."
cp /var/www/html/docker/nginx.conf /etc/nginx/sites-available/default

echo "==> Waiting for MySQL..."
until php -r "
try {
    new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4',
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
    );
    exit(0);
} catch (Exception \$e) { exit(1); }
"; do
    echo "  MySQL not ready, retrying in 3s..."
    sleep 3
done
echo "  MySQL is ready."

echo "==> Running php artisan package:discover..."
php artisan package:discover --ansi 2>/dev/null || true

if [ "$APP_INSTALLED" = "true" ]; then
    echo "==> App installed: running migrations and caching..."
    php artisan migrate --force --ansi 2>/dev/null || true
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
else
    echo "==> App not yet installed. Visit /installer/ to complete setup."
fi

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting services via Supervisor..."
exec /usr/bin/supervisord -c /var/www/html/docker/supervisord.conf
