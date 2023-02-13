# NGING + PHP-FPM + MariaDB

The fashionable setup. PHP 8.1 as PHP-FPM reverse proxied by NGINX and MariaDB as database. All in separate containers. Current directory mounted into webserver so code changes can be seen immediately. This requires you to install Composer dependencies locally in the host machine.

```
$ git clone https://github.com/tuupola/slim-docker.git
$ cd slim-docker/nginx-phpfpm
$ composer install
```

You can either run with docker compose for development.

```
$ docker compose up --build
```

Or as a docker stack which is a more production like setup. The stack has three instances of PHP 8.1 as PHP-FPM reverse proxied by NGINX load balanced by the swarm routing mesh. Single MariaDB instance also in the swarm

```
$ docker swarm init
$ docker stack deploy -c stack.yaml slim
```

Verify that the basic route is working.

```
$ curl --ipv4 --include example.localhost
HTTP/1.1 200 OK
Server: nginx/1.23.2
Date: Thu, 08 Dec 2022 09:38:50 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/8.1.13

Hello world!

$ curl --ipv4 --include example.localhost/mars
HTTP/1.1 200 OK
Server: nginx/1.23.2
Date: Thu, 08 Dec 2022 09:39:22 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/8.1.13

Hello mars!
```

Verify you can query the database successfully.

```
$ curl --ipv4 --include example.localhost/cars
HTTP/1.1 200 OK
Server: nginx/1.23.2
Date: Thu, 08 Dec 2022 09:39:43 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/8.1.13

Tesla Audi BMW
```

Verify that static files are being served.

```
$ curl --ipv4 --include example.localhost/static.html
HTTP/1.1 200 OK
Server: nginx/1.23.2
Date: Thu, 08 Dec 2022 09:53:04 GMT
Content-Type: text/html
Content-Length: 7
Last-Modified: Thu, 08 Dec 2022 09:52:52 GMT
Connection: keep-alive
ETag: "6391b3f4-7"
Accept-Ranges: bytes

static
```

You can also dump the `$_SERVER` superglobal for debugging purposes.

```
$ curl --ipv4 --include "example.localhost/server?foo=bar"
HTTP/1.1 200 OK
Server: nginx/1.23.2
Date: Thu, 08 Dec 2022 09:40:50 GMT
Content-Type: text/html; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/8.1.13

array (
  'HOSTNAME' => '45fa93f55fb2',
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
  'HTTP_USER_AGENT' => 'curl/7.82.0',
  'HTTP_HOST' => 'example.localhost',
  'REDIRECT_STATUS' => '200',
  'SERVER_NAME' => 'example.localhost',
  'SERVER_PORT' => '80',
  'SERVER_ADDR' => '172.29.0.3',
  'REMOTE_PORT' => '33554',
  'REMOTE_ADDR' => '172.29.0.1',
  'SERVER_SOFTWARE' => 'nginx/1.23.2',
  'GATEWAY_INTERFACE' => 'CGI/1.1',
  'REQUEST_SCHEME' => 'http',
  'SERVER_PROTOCOL' => 'HTTP/1.1',
  'DOCUMENT_ROOT' => '/srv/www/public',
  'DOCUMENT_URI' => '/index.php',
  'REQUEST_URI' => '/server?foo=bar',
  'SCRIPT_NAME' => '/index.php',
  'CONTENT_LENGTH' => '',
  'CONTENT_TYPE' => '',
  'REQUEST_METHOD' => 'GET',
  'QUERY_STRING' => 'foo=bar',
  'SCRIPT_FILENAME' => '/srv/www/public/index.php',
  'FCGI_ROLE' => 'RESPONDER',
  'PHP_SELF' => '/index.php',
  'REQUEST_TIME_FLOAT' => 1670492450.049713,
  'REQUEST_TIME' => 1670492450,
  'argv' =>
  array (
    0 => 'foo=bar',
  ),
  'argc' => 1,
)
```