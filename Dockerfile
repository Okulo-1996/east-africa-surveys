FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, but good to have)
RUN a2enmod rewrite

# Install PostgreSQL extensions for PHP
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql

# Copy all application files to the container
COPY . /var/www/html/

# Set proper permissions (simplified - no storage folder needed)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
