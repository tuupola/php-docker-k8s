# PHP with Docker and Kubernetes

![Docker + Kubernetes](https://www.appelsiini.net/img/docker-kubernetes.png)

Examples how to run [Slim Framework](https://www.slimframework.com/) with Docker and Kubernetes. Same setups should work with any PHP application. Subfolders have their own more specific README.

## Docker Compose

Use docker compose for development. The application source is bindmounted into the container so edits can be seen instantly.

```
$ docker compose up --build
```

## Docker Stack

Docker stack is usually used for deployment into a swarm. Although you could use it also for development. To use Docker stack you must have initialised a [Docker swarm](https://docs.docker.com/engine/swarm/) before deploying.

```
$ docker swarm init
$ docker stack deploy -c stack.yaml slim
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

[The simplest possible setup](apache/). PHP 8.1 as Apache an module and MariaDB in separate containers. This setup also has a Kubernetes example.

### NGINX + PHP-FPM + MariaDB

[The fashionable setup](nginx-phpfpm/). PHP 8.1 as PHP-FPM reverse proxied by NGINX and MariaDB as database. All in separate containers. This setup also has a Kubernetes example.

### Caddy + Apache + MariaDB

[Modern simple setup](caddy-apache/). Caddy as a reverse proxy in front of PHP 8.1 as an Apache module and MariaDB as the database. All in separate containers. With Docker Caddy handles TLS automatically. There is no Kubernetes example. With Kubernetes you would use ingress instead of manually installed Caddy.


### Caddy + PHP-FPM + MariaDB

[Modern setup](caddy-phpfpm/). PHP 8.1 as PHP-FPM reverse proxied by Caddy and MariaDB as database. All in separate containers. With Docker Caddy handles TLS automatically. TODO: add Kubernetes example.

### Traefik + Apache + MariaDB

[Enterprisey setup](traefik-apache/). Traefik as reverse proxy in front of PHP 8.1 as Apache an module and MariaDB as the database. With Docker Traefik handles TLS automatically. There is no Kubernetes example. With Kubernetes you would use ingress instead of manually installed Traefik.