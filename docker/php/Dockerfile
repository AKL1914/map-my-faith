FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    zip unzip curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    supervisor \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml

# Install phpredis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Nginx config
COPY default.conf /etc/nginx/conf.d/default.conf

# Copy Supervisor config
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy html directory locally, not requiring to mount an NFS
COPY laravel-app /var/www/html/

# Set working directory
WORKDIR /var/www/html

# run composer install
RUN composer install

# run composer update
RUN composer update

# Install NPM dependencies and build frontend assets for production
RUN npm install && npm run build

# Set permissions (adjust as needed)
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose HTTP Port
EXPOSE 80
EXPOSE 443

# Start supervisor instead of PHP-FPM directly
CMD ["/usr/bin/supervisord"]
