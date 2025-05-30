# Utilise l'image officielle PHP avec Apache
FROM php:8.2-apache

# Installe les extensions nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Active mod_rewrite
RUN a2enmod rewrite

# Copie le code source dans le conteneur
COPY . /var/www/html/

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

# Expose le port 80
EXPOSE 80
