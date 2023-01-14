<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require __DIR__ . "/vendor/autoload.php";

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get("/server", function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {
    $response->getBody()->write(var_export($_SERVER, true));
    return $response;
});

$app->get("/cars", function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $arguments
): ResponseInterface {

    $pdo = new PDO("mysql:host=mariadb;dbname=example", "user", "password");

    $statement = $pdo->prepare("SELECT brand FROM cars");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_OBJ);
    foreach ($result as $row) {
        $response->getBody()->write("{$row->brand} ");
    }
    return $response;
});

$app->get("/[{name}]", function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {
    $name = $args["name"] ?? "world";
    $response->getBody()->write("Hello {$name}!");
    return $response;
});

$app->run();