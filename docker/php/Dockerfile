FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip unzip curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install phpredis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Start supervisor instead of PHP-FPM directly
CMD ["/usr/bin/supervisord"]
