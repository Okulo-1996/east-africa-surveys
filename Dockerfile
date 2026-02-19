FROM php:8.2-apache

# Install PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql

# Copy files with correct permissions from the start
COPY --chown=www-data:www-data . /var/www/html/

# Make sure Apache can read everything
RUN chmod -R 755 /var/www/html

EXPOSE 80
