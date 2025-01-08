<?php

use DI\Container;
use Slim\Factory\AppFactory;

// Création du conteneur de dépendances
$container = new Container();

// Configuration des dépendances
$container->set('ExampleAction', function () {
    return new \Gateway\Actions\ExampleAction();
});

// Création de l'application Slim avec le conteneur
AppFactory::setContainer($container);
$app = AppFactory::create();

// Ajout des middlewares (optionnel)
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

return $app;