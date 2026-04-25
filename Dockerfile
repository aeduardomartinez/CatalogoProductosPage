FROM php:8.2-apache

# Install PostgreSQL client and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Ensure uploads directory exists and set permissions
RUN mkdir -p /var/www/html/uploads && chown -R www-data:www-data /var/www/html/uploads

WORKDIR /var/www/html/

# Use the PORT environment variable if provided (for Railway/Render), default to 80
CMD sed -i "s/80/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground
