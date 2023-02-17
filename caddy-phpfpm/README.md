# Caddy + PHP-FPM + MariaDB

Modern simple setup. PHP 8.1 as PHP-FPM reverse proxied by Caddy and MariaDB as database. All in separate containers. Caddy handles TLS automatically. This requires you to install Composer dependencies locally in the host machine.

```
$ git clone https://github.com/tuupola/slim-docker.git
$ cd slim-docker/caddy-phpfpm
$ composer install
```

You can either run with docker compose for development.

```
$ docker compose up --build
```

Or as a docker stack which is a more production like setup. The stack has a single MariaDB instance and three instances of PHP 8.1 as PHP-FPM load balanced by Caddy.

```
$ docker stack deploy -c stack.yaml slim
```

Verify that the basic route is working.

```
$ curl --ipv4 --include example.localhost
HTTP/1.1 308 Permanent Redirect
Connection: close
Location: https://example.localhost/
Server: Caddy
Date: Wed, 28 Dec 2022 10:26:36 GMT
Content-Length: 0

$ curl --ipv4 --include --insecure https://example.localhost
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
server: Caddy
x-powered-by: PHP/8.1.13
content-length: 12
date: Wed, 28 Dec 2022 10:27:13 GMT

Hello world!%


$ curl --ipv4 --include --insecure https://example.localhost/mars
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
server: Caddy
x-powered-by: PHP/8.1.13
content-length: 11
date: Wed, 28 Dec 2022 10:29:47 GMT

Hello mars!
```

Verify you can query the database successfully.

```
$ curl --ipv4 --include --insecure https://example.localhost/cars
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
server: Caddy
x-powered-by: PHP/8.1.13
content-length: 15
date: Wed, 28 Dec 2022 10:31:46 GMT

Tesla Audi BMW
```

Verify that static files are being served.

```
$ curl --ipv4 --include --insecure https://example.localhost/static.html
accept-ranges: bytes
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=utf-8
etag: "rnc56y7"
last-modified: Fri, 23 Dec 2022 08:35:22 GMT
server: Caddy
content-length: 7
date: Wed, 28 Dec 2022 10:32:26 GMT

static
```

You can also dump the `$_SERVER` superglobal for debugging purposes.

```
curl --ipv4 --include --insecure "https://example.localhost/server?foo=bar"
HTTP/2 200
alt-svc: h3=":443"; ma=2592000
content-type: text/html; charset=UTF-8
server: Caddy
x-powered-by: PHP/8.1.13
content-length: 2207
date: Wed, 28 Dec 2022 10:32:57 GMT

array (
  'HOSTNAME' => '6393933bcc51',
  'PHP_INI_DIR' => '/usr/local/etc/php',
  'SHLVL' => '1',
  'HOME' => '/home/www-data',
  'PHP_LDFLAGS' => '-Wl,-O1 -pie',
  'PHP_CFLAGS' => '-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64',
  'PHP_VERSION' => '8.1.13',
  'GPG_KEYS' => '528995BFEDFBA7191D46839EF9BA0ADA31CBD89E 39B641343D8C104B2B146DC3F9C39DC0B9698544 F1F692238FBC1666E5A5CCD4199F9DFEF6FFBAFD',
  'PHP_CPPFLAGS' => '-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64',
  'PHP_ASC_URL' => 'https://www.php.net/distributions/php-8.1.13.tar.xz.asc',
  'PHP_URL' => 'https://www.php.net/distributions/php-8.1.13.tar.xz',
  'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
  'PHPIZE_DEPS' => 'autoconf 		dpkg-dev dpkg 		file 		g++ 		gcc 		libc-dev 		make 		pkgconf 		re2c',
  'PWD' => '/srv/www',
  'PHP_SHA256' => 'b15ef0ccdd6760825604b3c4e3e73558dcf87c75ef1d68ef4289d8fd261ac856',
  'USER' => 'www-data',
  'HTTP_ACCEPT' => '*/*',
  'SERVER_SOFTWARE' => 'Caddy/v2.6.2',
  'REQUEST_METHOD' => 'GET',
  'SSL_CIPHER' => 'TLS_AES_128_GCM_SHA256',
  'SERVER_PROTOCOL' => 'HTTP/2.0',
  'REMOTE_USER' => '',
  'QUERY_STRING' => 'foo=bar',
  'GATEWAY_INTERFACE' => 'CGI/1.1',
  'HTTP_X_FORWARDED_FOR' => '172.18.0.1',
  'HTTP_USER_AGENT' => 'curl/7.82.0',
  'HTTP_X_FORWARDED_HOST' => 'example.localhost',
  'HTTPS' => 'on',
  'SERVER_NAME' => 'localhost',
  'REQUEST_SCHEME' => 'https',
  'REMOTE_IDENT' => '',
  'SERVER_PORT' => '443',
  'SCRIPT_NAME' => '/index.php',
  'SCRIPT_FILENAME' => '/srv/www/public/index.php',
  'HTTP_X_FORWARDED_PROTO' => 'https',
  'DOCUMENT_ROOT' => '/srv/www/public',
  'SSL_PROTOCOL' => 'TLSv1.3',
  'HTTP_HOST' => 'localhost',
  'REMOTE_HOST' => '172.18.0.1',
  'CONTENT_LENGTH' => '0',
  'AUTH_TYPE' => '',
  'REQUEST_URI' => '/server?foo=bar',
  'DOCUMENT_URI' => '/index.php',
  'REMOTE_PORT' => '33728',
  'REMOTE_ADDR' => '172.18.0.1',
  'PATH_INFO' => '',
  'CONTENT_TYPE' => '',
  'FCGI_ROLE' => 'RESPONDER',
  'PHP_SELF' => '/index.php',
  'REQUEST_TIME_FLOAT' => 1672223577.765711,
  'REQUEST_TIME' => 1672223577,
  'argv' =>
  array (
    0 => 'foo=bar',
  ),
  'argc' => 1,
)
```