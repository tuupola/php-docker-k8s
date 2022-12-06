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

$app->get("/[{name}]", function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {
    $name = $args["name"] ?? "world";
    $response->getBody()->write("hello $name");
    return $response;
});

$app->run();