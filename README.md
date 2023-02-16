# Slim Framework with Docker and Kubernetes

Examples how to run [Slim Framework](https://www.slimframework.com/) with Docker and Kubernetes. Same setups should work with any PHP application. Subfolders have their own more specific README.

## Docker Compose

Use docker compose for development. The application source is bindmounted into the container so edits can be seen instantly.

```
$ docker compose up --build
```

## Docker Stack

Docker stack is usually used for deployment into a swarm. Although you could use it also for development.

```
$ docker stack deploy -c stack.yaml slim
```

To use Docker stack you must have initialised atleast a single node [Docker swarm](https://docs.docker.com/engine/swarm/) before deploying.

```
$ docker swarm init
```

## Kubernetes

Config file for Kubernetes deployments is also provided for some of the examples.

```
$ kubectl apply -f deployment.yaml
```

Since Kubernetes does not bind ports to localhost, you should somehow make `example.local` resolv to the ingress ip address. Easiest way is to add it to the hosts file.

```
$ kubectl get ingress

NAME           CLASS     HOSTS           ADDRESS      PORTS   AGE
slim-ingress   traefik   example.local   172.23.0.2   80      70s

$ echo "172.23.0.2 example.local" | sudo tee --append /etc/hosts
```

There are several ways to create a local Kubernetes for testing. Easiest are probably [k3d](https://k3d.io/) and [Minikube](https://minikube.sigs.k8s.io/docs/start/). I have not tested, but [Docker Desktop](https://docs.docker.com/desktop/kubernetes/) most likely works too.

```
$ k3d cluster create slim-test
```

With Minikube remember to enable ingress addon first.

```
$ minikube addons enable ingress
$ minikube start
```

## Testing

After deploying you can test different endpoints with curl.

```
$ curl --ipv4 --include example.localhost
$ curl --ipv4 --include example.localhost/cars
$ curl --ipv4 --include example.localhost/static.html
$ curl --ipv4 --include example.localhost/server\?foo=bar
```

If testing Kubernetes deployment use `example.local` instead.

```
$ curl --ipv4 --include example.local
$ curl --ipv4 --include example.local/cars
$ curl --ipv4 --include example.local/static.html
$ curl --ipv4 --include example.local/server\?foo=bar
```

Note that some setups use TLS.

```
$ curl --ipv4 --include https://example.localhost
$ curl --ipv4 --include https://example.localhost/cars
$ curl --ipv4 --include https://example.localhost/static.html
$ curl --ipv4 --include https://example.localhost/server\?foo=bar
```

## Setups
### Apache + MariaDB

The simplest possible setup. PHP 8.1 as Apache an module and MariaDB in separate containers.

### NGINX + PHP-FPM + MariaDB

The fashionable setup. PHP 8.1 as PHP-FPM reverse proxied by NGINX and MariaDB as database. All in separate containers.

### Caddy + Apache + MariaDB

Caddy as a reverse proxy in front of PHP 8.1 as an Apache module and MariaDB as the database. All in separate containers. Caddy handles TLS automatically.

### Caddy + PHP-FPM + MariaDB

Modern simple setup. PHP 8.1 as PHP-FPM reverse proxied by Caddy and MariaDB as database. All in separate containers. Caddy handles TLS automatically.

### Traefik + Apache + MariaDB

Enterprisey setup. Traefik as reverse proxy in front of PHP 8.1 as Apache an module and MariaDB as the database. Traefik handles TLS automatically.