FROM php:8.2-apache

# Instalar la extensión MySQLi para conectar con la base de datos
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar reescritura de URLs de Apache
RUN a2enmod rewrite

# Copiar el código del proyecto a la carpeta pública de Apache
COPY . /var/www/html/

# Ajustar permisos para el servidor web
RUN chown -R www-data:www-data /var/www/html