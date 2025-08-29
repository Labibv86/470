FROM composer:2.6 AS build-stage
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Add Node.js stage
FROM node:18 AS frontend-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.2-apache
WORKDIR /var/www/html

# Install PHP extensions
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Enable Apache rewrite
RUN a2enmod rewrite

# Set Apache document root to public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy application files from build stages
COPY --from=build-stage /app .

# COPY THE ENTIRE BUILD DIRECTORY CORRECTLY
COPY --from=frontend-stage /app/public/build/ /var/www/html/public/build/

# Set proper permissions for build directory too
RUN chown -R www-data:www-data storage bootstrap/cache public/build
RUN chmod -R 775 storage bootstrap/cache public/build

RUN if [ ! -z "$DATABASE_URL" ]; then php artisan migrate --force; fi

# Add Apache configuration for build directory
RUN echo '<Directory "/var/www/html/public/build">' >> /etc/apache2/apache2.conf
RUN echo '    Options Indexes FollowSymLinks' >> /etc/apache2/apache2.conf
RUN echo '    AllowOverride None' >> /etc/apache2/apache2.conf
RUN echo '    Require all granted' >> /etc/apache2/apache2.conf
RUN echo '</Directory>' >> /etc/apache2/apache2.conf

EXPOSE 80
