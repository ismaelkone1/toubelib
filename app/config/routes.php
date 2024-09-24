<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use toubeelib\application\actions\ConsulterRendezVousAction;
use toubeelib\application\actions\HomeAction;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', HomeAction::class);

    $app->get('/rdvs/{ID-RDV}', ConsulterRendezVousAction::class);

    return $app;
};