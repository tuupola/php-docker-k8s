# This is used with docker compose. The image has also been pushed
# to GitHub Container Registry.
#
# $ docker pull ghcr.io/tuupola/apache-php-example:latest

FROM php:8.1-apache
WORKDIR /srv/www

ENV APACHE_DOCUMENT_ROOT /srv/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql

# Compose overrides this with a bind mount for easy dev.
COPY ./ /srv/www/
