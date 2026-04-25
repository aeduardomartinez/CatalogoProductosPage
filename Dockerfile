FROM php:8.2-apache

# 1. Instalar PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# 2. Configurar Apache
RUN a2enmod rewrite

# 3. Copiar archivos
COPY . /var/www/html/

# 4. Carpeta de imágenes
RUN mkdir -p /var/www/html/uploads && chown -R www-data:www-data /var/www/html/uploads

# 5. Puerto fijo para Railway
EXPOSE 80

WORKDIR /var/www/html/
