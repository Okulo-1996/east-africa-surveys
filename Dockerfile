FROM php:8.2-apache

# Install PostgreSQL extensions
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql && \
    docker-php-ext-enable pdo_pgsql

# Enable Apache modules
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# CRITICAL FIX: Set correct permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Configure Apache to use index.php as default
RUN echo "DirectoryIndex index.php" > /etc/apache2/conf-available/directory-index.conf && \
    a2enconf directory-index

# Ensure Apache can read all files
RUN chmod 755 /var/www/html

# Expose port 80
EXPOSE 80
