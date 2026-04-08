FROM php:8.2-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    libicu-dev libgmp-dev libcurl4-openssl-dev \
    zip unzip nginx supervisor ffmpeg \
    && rm -rf /var/lib/apt/lists/*

# Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions (all required by Laravel + packages)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring exif pcntl bcmath zip intl \
        gd curl dom xml fileinfo

# PHP memory limit
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy full application (needed because composer merge-plugin reads modules/*/composer.json)
COPY . .

# PHP dependencies (--no-scripts: skip artisan package:discover, runs at startup)
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --optimize-autoloader --no-dev --no-interaction --no-scripts

# JS assets build
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Supervisor log dir + startup script
RUN mkdir -p /var/log/supervisor \
    && chmod +x /var/www/html/docker/startup.sh

EXPOSE 8080

CMD ["/var/www/html/docker/startup.sh"]
