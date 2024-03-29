# Caddy + Apache + MariaDB

Caddy as a reverse proxy in front of PHP 8.1 as an Apache module and MariaDB as the database. All in separate containers. This requires you to install Composer dependencies locally in the host machine.

```
$ git clone https://github.com/tuupola/slim-docker.git
$ cd slim-docker/caddy-apache
$ composer install
```

You can either run with docker compose for development.

```
$ docker compose up --build
```

Or as a docker stack which is a more production like setup. The stack has a single MariaDB instance and three instances of PHP 8.1 as an Apache module load balanced by Caddy.

```
$ docker stack deploy -c stack.yaml slim
```

Verify that the basic route is working. Caddy automatically forces TLS.

```
$ curl --ipv4 --include http://example.localhost
HTTP/1.1 308 Permanent Redirect
Connection: close
Location: https://example.localhost/
Server: Caddy
Date: Tue, 13 Dec 2022 07:41:39 GMT
Content-Length: 0

$ curl --ipv4 --include --insecure https://example.localhost
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
date: Tue, 13 Dec 2022 07:42:23 GMT
server: Caddy
server: Apache/2.4.54 (Debian)
x-powered-by: PHP/8.1.13
content-length: 12

Hello world!

$ curl --ipv4 --include --insecure https://example.localhost/mars
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
date: Tue, 13 Dec 2022 07:42:57 GMT
server: Caddy
server: Apache/2.4.54 (Debian)
x-powered-by: PHP/8.1.13
content-length: 11

Hello mars!
```

Verify you can query the database successfully.

```
$ curl --ipv4 --include --insecure https://example.localhost/cars
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
date: Tue, 13 Dec 2022 07:43:14 GMT
server: Caddy
server: Apache/2.4.54 (Debian)
x-powered-by: PHP/8.1.13
content-length: 15


Tesla Audi BMW
```

Verify that the static files are being served.

```
$ curl --ipv4 --include --insecure https://example.localhost/static.html
HTTP/2 200
accept-ranges: bytes
alt-svc: h3=":443"; ma=2592000
content-type: text/html
date: Tue, 13 Dec 2022 07:43:47 GMT
etag: "7-5ef4e0022ff77"
last-modified: Thu, 08 Dec 2022 09:52:52 GMT
server: Caddy
server: Apache/2.4.54 (Debian)
content-length: 7

static
```

You can also dump the `$_SERVER` superglobal for debugging purposes.

```
$ curl --ipv4 --include --insecure "https://example.localhost/server?foo=bar"
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
date: Tue, 13 Dec 2022 07:44:43 GMT
server: Caddy
server: Apache/2.4.54 (Debian)
vary: Accept-Encoding
x-powered-by: PHP/8.1.13

array (
  'REDIRECT_STATUS' => '200',
  'HTTP_HOST' => 'example.localhost',
  'HTTP_USER_AGENT' => 'curl/7.82.0',
  'HTTP_ACCEPT' => '*/*',
  'HTTP_X_FORWARDED_FOR' => '172.21.0.1',
  'HTTP_X_FORWARDED_HOST' => 'example.localhost',
  'HTTP_X_FORWARDED_PROTO' => 'https',
  'HTTP_ACCEPT_ENCODING' => 'gzip',
  'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
  'SERVER_SIGNATURE' => '<address>Apache/2.4.54 (Debian) Server at example.localhost Port 80</address>
',
  'SERVER_SOFTWARE' => 'Apache/2.4.54 (Debian)',
  'SERVER_NAME' => 'example.localhost',
  'SERVER_ADDR' => '172.21.0.3',
  'SERVER_PORT' => '80',
  'REMOTE_ADDR' => '172.21.0.2',
  'DOCUMENT_ROOT' => '/srv/www/public',
  'REQUEST_SCHEME' => 'http',
  'CONTEXT_PREFIX' => '',
  'CONTEXT_DOCUMENT_ROOT' => '/srv/www/public',
  'SERVER_ADMIN' => 'webmaster@localhost',
  'SCRIPT_FILENAME' => '/srv/www/public/index.php',
  'REMOTE_PORT' => '59476',
  'REDIRECT_URL' => '/server',
  'REDIRECT_QUERY_STRING' => 'foo=bar',
  'GATEWAY_INTERFACE' => 'CGI/1.1',
  'SERVER_PROTOCOL' => 'HTTP/1.1',
  'REQUEST_METHOD' => 'GET',
  'QUERY_STRING' => 'foo=bar',
  'REQUEST_URI' => '/server?foo=bar',
  'SCRIPT_NAME' => '/index.php',
  'PHP_SELF' => '/index.php',
  'REQUEST_TIME_FLOAT' => 1673948267.53973,
  'REQUEST_TIME' => 1673948267,
  'argv' =>
  array (
    0 => 'foo=bar',
  ),
  'argc' => 1,
)
```