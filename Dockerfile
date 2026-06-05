FROM php:8.2-apache

# Copie tout le contenu de votre projet dans le serveur de Render
COPY . /var/www/html/

# Dit à Apache d'écouter sur le port que Render lui donne
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80