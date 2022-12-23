# Traefik + Slim + Apache + MariaDB

Traefik reverse proxy in front of PHP 8.1 as Apache an module and MariaDB as database. Current directory mounted into webserver so code changes can be seen immediately. All in separate containers. This requires you to install Composer dependencies locally in the host machine.

```
$ git clone https://github.com/tuupola/slim-docker.git
$ cd slim-docker
$ git checkout traefik-apache-php
$ composer install
$ docker compose build
$ docker compose up
```

Verify you can access the [dashboard](http://traefik.localhost/dashboard/).

```
$ curl --ipv4 --include traefik.localhost
HTTP/1.1 302 Found
Content-Type: text/html; charset=utf-8
Location: /dashboard/
Date: Sun, 11 Dec 2022 13:54:45 GMT
Content-Length: 34

<a href="/dashboard/">Found</a>.
```

Verify that the [basic route](https://github.com/tuupola/slim-docker/blob/apache-php/app.php#L43-L51) is working.

```
$ curl --ipv4 --include slim.localhost
HTTP/1.1 200 OK
Content-Length: 12
Content-Type: text/html; charset=UTF-8
Date: Sun, 11 Dec 2022 10:17:27 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/8.1.13

Hello world!

$ curl --ipv4 --include slim.localhost/mars
HTTP/1.1 200 OK
Content-Length: 11
Content-Type: text/html; charset=UTF-8
Date: Sun, 11 Dec 2022 10:17:47 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/8.1.13

Hello mars!
```

Verify you can [query the database](https://github.com/tuupola/slim-docker/blob/apache-php/app.php#L26-L41) successfully.

```
$ curl --ipv4 --include slim.localhost/cars
HTTP/1.1 200 OK
Content-Length: 15
Content-Type: text/html; charset=UTF-8
Date: Sun, 11 Dec 2022 10:18:59 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/8.1.13

Tesla Audi BMW
```

Verify that [static files](https://github.com/tuupola/slim-docker/blob/apache-php/public/static.html) are being served.

```
$ curl --ipv4 --include slim.localhost/static.html
HTTP/1.1 200 OK
Accept-Ranges: bytes
Content-Length: 7
Content-Type: text/html
Date: Sun, 11 Dec 2022 10:19:17 GMT
Etag: "7-5ef4e0022ff77"
Last-Modified: Thu, 08 Dec 2022 09:52:52 GMT
Server: Apache/2.4.54 (Debian)

static
```

You can also [dump the `$_SERVER`](https://github.com/tuupola/slim-docker/blob/apache-php/app.php#L17-L24) superglobal for debugging purposes.

```
curl --ipv4 --include "slim.localhost/server?foo=bar"
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
Date: Sun, 11 Dec 2022 10:19:45 GMT
Server: Apache/2.4.54 (Debian)
Vary: Accept-Encoding
X-Powered-By: PHP/8.1.13
Transfer-Encoding: chunked

array (
  'REDIRECT_STATUS' => '200',
  'HTTP_HOST' => 'slim.localhost',
  'HTTP_USER_AGENT' => 'curl/7.82.0',
  'HTTP_ACCEPT' => '*/*',
  'HTTP_X_FORWARDED_FOR' => '192.168.96.1',
  'HTTP_X_FORWARDED_HOST' => 'slim.localhost',
  'HTTP_X_FORWARDED_PORT' => '80',
  'HTTP_X_FORWARDED_PROTO' => 'http',
  'HTTP_X_FORWARDED_SERVER' => '21abf0a83bbf',
  'HTTP_X_REAL_IP' => '192.168.96.1',
  'HTTP_ACCEPT_ENCODING' => 'gzip',
  'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
  'SERVER_SIGNATURE' => '<address>Apache/2.4.54 (Debian) Server at slim.localhost Port 80</address>
',
  'SERVER_SOFTWARE' => 'Apache/2.4.54 (Debian)',
  'SERVER_NAME' => 'slim.localhost',
  'SERVER_ADDR' => '192.168.96.2',
  'SERVER_PORT' => '80',
  'REMOTE_ADDR' => '192.168.96.3',
  'DOCUMENT_ROOT' => '/srv/www/public',
  'REQUEST_SCHEME' => 'http',
  'CONTEXT_PREFIX' => '',
  'CONTEXT_DOCUMENT_ROOT' => '/srv/www/public',
  'SERVER_ADMIN' => 'webmaster@localhost',
  'SCRIPT_FILENAME' => '/srv/www/public/index.php',
  'REMOTE_PORT' => '49630',
  'REDIRECT_URL' => '/server',
  'REDIRECT_QUERY_STRING' => 'foo=bar',
  'GATEWAY_INTERFACE' => 'CGI/1.1',
  'SERVER_PROTOCOL' => 'HTTP/1.1',
  'REQUEST_METHOD' => 'GET',
  'QUERY_STRING' => 'foo=bar',
  'REQUEST_URI' => '/server?foo=bar',
  'SCRIPT_NAME' => '/index.php',
  'PHP_SELF' => '/index.php',
  'REQUEST_TIME_FLOAT' => 1670753985.331875,
  'REQUEST_TIME' => 1670753985,
  'argv' =>
  array (
    0 => 'foo=bar',
  ),
  'argc' => 1,
)
```