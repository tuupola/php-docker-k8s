# Traefik + Slim + Apache + MariaDB

Traefik reverse proxy in front of PHP 8.1 as Apache an module and MariaDB as database. Traefik will force https and uses a self generated cert. Current directory mounted into webserver so code changes can be seen immediately. All in separate containers. This requires you to install Composer dependencies locally in the host machine.

```
$ git clone https://github.com/tuupola/slim-docker.git
$ cd slim-docker
$ git checkout traefik-apache-php
$ composer install
```

You can either run with docker compose for development.

```
$ docker compose build
$ docker compose up
```

Or as a docker stack which is a more production like setup. The stack has three instances of PHP 8.1 as an Apache module load balanced by Traefik.

```
$ docker swarm init
$ docker compose build
$ docker stack deploy -c stack.yaml slim
```

Verify you can access the [dashboard](http://traefik.localhost/dashboard/).

```
$ curl --ipv4 --include traefik.localhost
HTTP/1.1 301 Moved Permanently
Location: https://traefik.localhost/
Date: Fri, 23 Dec 2022 09:49:19 GMT
Content-Length: 17
Content-Type: text/plain; charset=utf-8

Moved Permanently


$ curl --ipv4 --include --insecure https://traefik.localhost
HTTP/2 302
content-type: text/html; charset=utf-8
location: /dashboard/
content-length: 34
date: Fri, 23 Dec 2022 09:50:11 GMT

<a href="/dashboard/">Found</a>.

```

Verify that the [basic route](https://github.com/tuupola/slim-docker/blob/apache-php/app.php#L43-L51) is working.

```
$ curl --ipv4 --include --insecure https://apache.localhost
HTTP/2 200
content-type: text/html; charset=UTF-8
date: Fri, 23 Dec 2022 09:51:33 GMT
server: Apache/2.4.54 (Debian)
x-powered-by: PHP/8.1.13
content-length: 12

Hello world!

$ curl --ipv4 --include --insecure https://apache.localhost/mars
HTTP/2 200
content-type: text/html; charset=UTF-8
date: Fri, 23 Dec 2022 09:52:55 GMT
server: Apache/2.4.54 (Debian)
x-powered-by: PHP/8.1.13
content-length: 11

Hello mars!
```

Verify you can [query the database](https://github.com/tuupola/slim-docker/blob/apache-php/app.php#L26-L41) successfully.

```
$ curl --ipv4 --include --insecure https://apache.localhost/cars
HTTP/2 200
content-type: text/html; charset=UTF-8
date: Fri, 23 Dec 2022 09:53:14 GMT
server: Apache/2.4.54 (Debian)
x-powered-by: PHP/8.1.13
content-length: 15

Tesla Audi BMW
```

Verify that [static files](https://github.com/tuupola/slim-docker/blob/apache-php/public/static.html) are being served.

```
$ curl --ipv4 --include --insecure https://apache.localhost/static.html
HTTP/2 200
accept-ranges: bytes
content-type: text/html
date: Fri, 23 Dec 2022 09:53:33 GMT
etag: "7-5f07aaaa0c423"
last-modified: Fri, 23 Dec 2022 08:35:22 GMT
server: Apache/2.4.54 (Debian)
content-length: 7

static
```

You can also [dump the `$_SERVER`](https://github.com/tuupola/slim-docker/blob/apache-php/app.php#L17-L24) superglobal for debugging purposes.

```
$ curl --ipv4 --include --insecure "https://apache.localhost/server?foo=bar"
HTTP/2 200
content-type: text/html; charset=UTF-8
date: Fri, 23 Dec 2022 09:56:00 GMT
server: Apache/2.4.54 (Debian)
vary: Accept-Encoding
x-powered-by: PHP/8.1.13

array (
  'REDIRECT_STATUS' => '200',
  'HTTP_HOST' => 'apache.localhost',
  'HTTP_USER_AGENT' => 'curl/7.82.0',
  'HTTP_ACCEPT' => '*/*',
  'HTTP_X_FORWARDED_FOR' => '172.18.0.1',
  'HTTP_X_FORWARDED_HOST' => 'apache.localhost',
  'HTTP_X_FORWARDED_PORT' => '443',
  'HTTP_X_FORWARDED_PROTO' => 'https',
  'HTTP_X_FORWARDED_SERVER' => 'cd59b246c046',
  'HTTP_X_REAL_IP' => '172.18.0.1',
  'HTTP_ACCEPT_ENCODING' => 'gzip',
  'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
  'SERVER_SIGNATURE' => '<address>Apache/2.4.54 (Debian) Server at apache.localhost Port 80</address>
',
  'SERVER_SOFTWARE' => 'Apache/2.4.54 (Debian)',
  'SERVER_NAME' => 'apache.localhost',
  'SERVER_ADDR' => '172.18.0.2',
  'SERVER_PORT' => '80',
  'REMOTE_ADDR' => '172.18.0.3',
  'DOCUMENT_ROOT' => '/srv/www/public',
  'REQUEST_SCHEME' => 'http',
  'CONTEXT_PREFIX' => '',
  'CONTEXT_DOCUMENT_ROOT' => '/srv/www/public',
  'SERVER_ADMIN' => 'webmaster@localhost',
  'SCRIPT_FILENAME' => '/srv/www/public/index.php',
  'REMOTE_PORT' => '46696',
  'REDIRECT_URL' => '/server',
  'REDIRECT_QUERY_STRING' => 'foo=bar',
  'GATEWAY_INTERFACE' => 'CGI/1.1',
  'SERVER_PROTOCOL' => 'HTTP/1.1',
  'REQUEST_METHOD' => 'GET',
  'QUERY_STRING' => 'foo=bar',
  'REQUEST_URI' => '/server?foo=bar',
  'SCRIPT_NAME' => '/index.php',
  'PHP_SELF' => '/index.php',
  'REQUEST_TIME_FLOAT' => 1671789360.247337,
  'REQUEST_TIME' => 1671789360,
  'argv' =>
  array (
    0 => 'foo=bar',
  ),
  'argc' => 1,
)
```