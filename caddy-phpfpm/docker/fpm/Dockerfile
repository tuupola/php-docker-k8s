# This is used with docker compose. The image has also been pushed
# to GitHub Container Registry.
#
# $ docker pull ghcr.io/tuupola/php-fpm-example

FROM php:8.1-fpm-alpine
WORKDIR /srv/www

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql

# Compose overrides this with a bind mount for easy dev.
COPY ./ /srv/www/
