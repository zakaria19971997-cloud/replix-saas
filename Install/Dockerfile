FROM php:8.2-fpm

# System dependencies (including libicu-dev for intl, libfreetype6-dev for gd)
RUN apt-get update && apt-get install -y \
    git curl \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    libicu-dev libgmp-dev \
    zip unzip nginx supervisor ffmpeg \
    && rm -rf /var/lib/apt/lists/*

# Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Increase PHP memory limit for composer
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy only composer files first (layer cache)
COPY composer.json composer.lock ./

# PHP dependencies (no dev) - with memory limit
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --optimize-autoloader --no-dev --no-interaction --no-scripts

# Copy full application
COPY . .

# Run composer post-install scripts
RUN COMPOSER_MEMORY_LIMIT=-1 composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# JS assets build
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Supervisor log directory
RUN mkdir -p /var/log/supervisor

# Make startup script executable
RUN chmod +x /var/www/html/docker/startup.sh

EXPOSE 8080

CMD ["/var/www/html/docker/startup.sh"]
