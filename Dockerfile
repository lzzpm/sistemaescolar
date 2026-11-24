#Utiliza PHP 8.2 con Apache como base para el proyecto
FROM php:8.2-apache

#Instala las extensiones necesarias para conectar PHP con MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

#Activa funciones de Apache para mejorar el funcionamiento de la aplicación
RUN a2enmod rewrite

#Copia todos los archivos del proyecto al servidor web
COPY . /var/www/html/

#Asigna los permisos necesarios para que Apache pueda acceder a los archivos
RUN chown -R www-data:www-data /var/www/html