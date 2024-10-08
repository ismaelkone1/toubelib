<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use toubeelib\application\actions\AnnulerRendezVous;
use toubeelib\application\actions\ConsulterRendezVousAction;
use toubeelib\application\actions\HomeAction;
use toubeelib\application\actions\ModifierRendezVousAction;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', HomeAction::class)
    ->add(new \app\middlewares\AddHearders);

    $app->get('/rdvs/{ID-RDV}', ConsulterRendezVousAction::class
    ->add(new \app\middlewares\AddHearders));

    $app->patch('/rdvs/{ID-RDV}', ModifierRendezVousAction::class
    ->add(new \app\middlewares\AddHearders));

    $app->delete('/rdvs/{ID-RDV}', AnnulerRendezVous::class
    ->add(new \app\middlewares\AddHearders));

    return $app;
};