# This is used with docker compose. The image has also been pushed
# to GitHub Container Registry.
#
# $ docker pull ghcr.io/tuupola/nginx-php-example

FROM nginx:alpine
WORKDIR /srv/www

# Compose overrides these with a bind mount for easy dev.
COPY ./ /srv/www/
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
