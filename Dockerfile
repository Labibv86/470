FROM composer:2.6
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

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

# Copy application files
COPY --from=0 /app .

# Copy CSS files from resources to public (THIS IS THE KEY LINE)
RUN mkdir -p public/css && cp -r resources/css/* public/css/

# Set proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache public/css
RUN chmod -R 775 storage bootstrap/cache public/css

EXPOSE 80
