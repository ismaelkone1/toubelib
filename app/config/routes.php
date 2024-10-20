<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use toubeelib\application\actions\GererCycleRendezVousAction;
use toubeelib\application\actions\ListerDispoPraticienAction;
use toubeelib\application\middlewares\AddHeaders;
use toubeelib\application\actions\AnnulerRendezVousAction;
use toubeelib\application\actions\ConsulterRendezVousAction;
use toubeelib\application\actions\HomeAction;
use toubeelib\application\actions\ModifierRendezVousAction;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', HomeAction::class)
    ->add(new AddHeaders);

    $app->get('/rdvs/{ID-RDV}', ConsulterRendezVousAction::class)
    ->add(new AddHeaders);

    $app->patch('/rdvs/{ID-RDV}', ModifierRendezVousAction::class)
    ->add(new AddHeaders);

    $app->patch('/rdvs/{ID-RDV}/annuler', AnnulerRendezVousAction::class)
    ->add(new AddHeaders);

    //La route s'utilise de la manière suivante : /praticiens/{ID-PRATICIEN}/disponibilites?debut=2021-06-01T08:00:00&fin=2021-06-01T18:00:00
    $app->get('/praticiens/{ID-PRATICIEN}/disponibilites', ListerDispoPraticienAction::class);

    //La route s'utilise de la manière suivante : /rdvs/{ID-RDV}/cycle?statut=2 pour mettre le rdv en "honore"
    $app->patch('/rdvs/{ID-RDV}/cycle', GererCycleRendezVousAction::class);

    return $app;
};