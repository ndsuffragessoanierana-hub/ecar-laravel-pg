FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Laravel optimizations (IMPORTANT)
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear

# Generate cache (safe if APP_KEY exists in ENV)
RUN php artisan config:cache

EXPOSE 10000

# IMPORTANT: use PHP-FPM (NOT artisan serve)
CMD ["php-fpm"]