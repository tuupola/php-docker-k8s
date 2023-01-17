# Slim Framework with Docker

Examples how to run [Slim Framework](https://www.slimframework.com/) with Docker. Same setups should work with any PHP application. Subfolders have their own more specific README.

## Apache + MariaDB

The simplest possible setup. PHP 8.1 as Apache an module and MariaDB in separate containers.

## NGINX + PHP-FPM + MariaDB

The fashionable setup. PHP 8.1 as PHP-FPM reverse proxied by NGINX and MariaDB as database. All in separate containers.

## Caddy + Apache + MariaDB

Caddy as a reverse proxy in front of PHP 8.1 as an Apache module and MariaDB as the database. All in separate containers. Caddy handles TLS automatically.

## Caddy + PHP-FPM + MariaDB

Modern simple setup. PHP 8.1 as PHP-FPM reverse proxied by Caddy and MariaDB as database. All in separate containers. Caddy handles TLS automatically.

## Traefik + Apache + MariaDB

Enterprisey setup. Traefik as reverse proxy in front of PHP 8.1 as Apache an module and MariaDB as the database. Traefik handles TLS automatically.