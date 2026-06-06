dockerfile
FROM php:8.2-apache

# Active le module rewrite d'Apache
RUN a2enmod rewrite

# Active l'extension MySQL
RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80
