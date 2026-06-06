FROM php:8.2-apache

# 1. Active le module de réécriture d'Apache (nécessaire pour le .htaccess)
RUN a2enmod rewrite

# 2. Si votre site utilise une base de données MySQL via PDO, activez cette extension :
RUN docker-php-ext-install pdo pdo_mysql

# 3. Copie votre code et configure le port dynamique de Render
COPY . /var/www/html/
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80