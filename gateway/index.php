<?php

use Slim\Factory\AppFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

require __DIR__ . '/vendor/autoload.php';

// Création de la fabrique PSR-17
$psr17Factory = new Psr17Factory();

// Configuration de l'application Slim avec la fabrique PSR-17
AppFactory::setResponseFactory($psr17Factory);
AppFactory::setStreamFactory($psr17Factory);

// Création de l'application Slim
$app = AppFactory::create();

// Création de la requête serveur avec nyholm/psr7-server
$serverRequestCreator = new ServerRequestCreator(
    $psr17Factory, // RequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);

// Configuration des routes
require __DIR__ . '/config/routes.php';

// Démarrage de l'application
$app->run();