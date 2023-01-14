FROM php:8.1-fpm-alpine
WORKDIR /srv/www

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql
