FROM php:8.2-apache

# 1. Instalar dependencias de PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Configurar Apache: Desactivar módulos conflictivos y activar rewrite
RUN a2dismod mpm_event mpm_worker || true && \
    a2enmod mpm_prefork rewrite

# 3. Copiar archivos
COPY . /var/www/html/

# 4. Permisos de la carpeta de imágenes
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads

# 5. Configurar puertos dinámicos para Railway/Render
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html/

CMD ["apache2-foreground"]
