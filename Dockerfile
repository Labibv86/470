FROM composer:2.6 AS build-stage
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-apache
WORKDIR /var/www/html

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy application files
COPY --from=build-stage /app .
COPY ./.htaccess /var/www/html/public/.htaccess

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage
