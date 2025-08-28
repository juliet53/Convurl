FROM php:8.2-apache

# Télécharger le script install-php-extensions et le rendre exécutable
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Installer les extensions PHP souhaitées : intl et pdo_mysql
RUN install-php-extensions intl pdo_mysql

# Installer Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

# Copier le projet dans le conteneur
COPY . /var/www/

# Permissions pour Symfony (logs et cache)
RUN mkdir -p /var/www/var/log /var/www/var/cache && \
    chown -R www-data:www-data /var/www/var && \
    chmod -R 775 /var/www/var

# Copier la config Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Installer les dépendances Composer
RUN cd /var/www && composer install

# Définir le répertoire de travail
WORKDIR /var/www/

ENTRYPOINT ["bash", "./docker/docker.sh"]

# Exposer le port 80
EXPOSE 80
