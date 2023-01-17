# Apache + MariaDB

The simplest possible development setup. PHP 8.1 as Apache an module and MariaDB in separate container. Current directory mounted into webserver so code changes can be seen immediately. This requires you to install Composer dependencies locally in the host machine.

```
$ git clone https://github.com/tuupola/slim-docker.git
$ cd slim-docker/apache
$ composer install
```

You can either run with docker compose for development.

```
$ docker compose build
$ docker compose up
```

Or as a docker stack which is a more production like setup. The stack has three instances of PHP 8.1 as an Apache module load balanced by the swarm routing mesh. Single MariaDB instance also in the swarm.

```
$ docker compose build
$ docker stack deploy -c stack.yaml slim
```

Verify that the basic route is working.

```
$ curl --ipv4 --include example.localhost
HTTP/1.1 200 OK
Date: Thu, 08 Dec 2022 09:00:54 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/8.1.13
Content-Length: 12
Content-Type: text/html; charset=UTF-8

Hello world!

$ curl --ipv4 --include example.localhost/mars
HTTP/1.1 200 OK
Date: Thu, 08 Dec 2022 09:01:29 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/8.1.13
Content-Length: 11
Content-Type: text/html; charset=UTF-8

Hello mars!
```

Verify you can query the database successfully.

```
$ curl --ipv4 --include example.localhost/cars
HTTP/1.1 200 OK
Date: Thu, 08 Dec 2022 09:03:06 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/8.1.13
Content-Length: 15
Content-Type: text/html; charset=UTF-8

Tesla Audi BMW
```

Verify that the static files are being served.

```
$ curl --ipv4 --include example.localhost/static.html
HTTP/1.1 200 OK
Date: Thu, 08 Dec 2022 09:10:43 GMT
Server: Apache/2.4.54 (Debian)
Last-Modified: Wed, 07 Dec 2022 15:47:10 GMT
ETag: "7-5ef3ed55ce837"
Accept-Ranges: bytes
Content-Length: 7
Content-Type: text/html

static
```

You can also dump the `$_SERVER` superglobal for debugging purposes.

```
$ curl --ipv4 --include "example.localhost/server?foo=bar"
HTTP/1.1 200 OK
Date: Thu, 08 Dec 2022 09:11:20 GMT
Server: Apache/2.4.54 (Debian)
X-Powered-By: PHP/8.1.13
Vary: Accept-Encoding
Content-Length: 1189
Content-Type: text/html; charset=UTF-8

array (
  'REDIRECT_STATUS' => '200',
  'HTTP_HOST' => 'example.localhost',
  'HTTP_USER_AGENT' => 'curl/7.82.0',
  'HTTP_ACCEPT' => '*/*',
  'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
  'SERVER_SIGNATURE' => '<address>Apache/2.4.54 (Debian) Server at example.localhost Port 80</address>
',
  'SERVER_SOFTWARE' => 'Apache/2.4.54 (Debian)',
  'SERVER_NAME' => 'example.localhost',
  'SERVER_ADDR' => '172.29.0.2',
  'SERVER_PORT' => '80',
  'REMOTE_ADDR' => '172.29.0.1',
  'DOCUMENT_ROOT' => '/srv/www/public',
  'REQUEST_SCHEME' => 'http',
  'CONTEXT_PREFIX' => '',
  'CONTEXT_DOCUMENT_ROOT' => '/srv/www/public',
  'SERVER_ADMIN' => 'webmaster@localhost',
  'SCRIPT_FILENAME' => '/srv/www/public/index.php',
  'REMOTE_PORT' => '57158',
  'REDIRECT_URL' => '/server',
  'REDIRECT_QUERY_STRING' => 'foo=bar',
  'GATEWAY_INTERFACE' => 'CGI/1.1',
  'SERVER_PROTOCOL' => 'HTTP/1.1',
  'REQUEST_METHOD' => 'GET',
  'QUERY_STRING' => 'foo=bar',
  'REQUEST_URI' => '/server?foo=bar',
  'SCRIPT_NAME' => '/index.php',
  'PHP_SELF' => '/index.php',
  'REQUEST_TIME_FLOAT' => 1670490680.927597,
  'REQUEST_TIME' => 1670490680,
  'argv' =>
  array (
    0 => 'foo=bar',
  ),
  'argc' => 1,
)
```