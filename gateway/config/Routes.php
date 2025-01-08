<?php

use Slim\Routing\RouteCollectorProxy;

$app->get('/', \Gateway\Actions\ExampleAction::class);

// Exemple d'une autre route
$app->get('/hello', function ($request, $response) {
    $response->getBody()->write("Hello, World!");
    return $response;
});