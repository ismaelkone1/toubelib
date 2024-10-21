<?php
declare(strict_types=1);

use toubeelib\application\actions\ConsulterListePraticiensAction;
use toubeelib\application\actions\ConsulterPlanningPraticienAction;
use toubeelib\application\actions\ConsulterPraticienAction;
use toubeelib\application\actions\GererCycleRendezVousAction;
use toubeelib\application\actions\ListerDispoPraticienAction;
use toubeelib\application\actions\ListerRendezVousPatientAction;
use toubeelib\application\middlewares\AddHeaders;
use toubeelib\application\actions\AnnulerRendezVousAction;
use toubeelib\application\actions\ConsulterRendezVousAction;
use toubeelib\application\actions\CreerRendezVousAction;
use toubeelib\application\actions\HomeAction;
use toubeelib\application\actions\ModifierRendezVousAction;
use toubeelib\application\actions\SigninAction;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', HomeAction::class)
    ->add(new AddHeaders);

    $app->post('/auth/signin', SigninAction::class)
    ->add(new AddHeaders);

    $app->post('/prendre-rdvs', CreerRendezVousAction::class)
    ->add(new AddHeaders);

    $app->get('/rdvs/{ID-RDV}', ConsulterRendezVousAction::class)
    ->add(new AddHeaders);

    $app->patch('/rdvs/{ID-RDV}', ModifierOuGererCycleRendezVousAction::class)
    ->add(new AddHeaders);

    $app->delete('/rdvs/{ID-RDV}', AnnulerRendezVousAction::class)
    ->add(new AddHeaders);

    //La route s'utilise de la manière suivante : /praticiens/{ID-PRATICIEN}/disponibilites?debut=2021-06-01T08:00:00&fin=2021-06-01T18:00:00
    $app->get('/praticiens/{ID-PRATICIEN}/disponibilites', ListerDispoPraticienAction::class)
    ->add(new AddHeaders);

    //La route s'utilise de la manière suivante : /rdvs/{ID-RDV}/cycle?statut=2 pour mettre le rdv en "honore"
    $app->patch('/rdvs/{ID-RDV}/cycle', GererCycleRendezVousAction::class)
    ->add(new AddHeaders);

    $app->get('/praticiens/{ID-PRATICIEN}', ConsulterPraticienAction::class)
    ->add(new AddHeaders);

    $app->get('/praticiens', ConsulterListePraticiensAction::class)
    ->add(new AddHeaders);

    //La route s'utilise de la manière suivante : /praticiens/4g5h6i7j-8901-1121-3141-6171k9l0m1n2/planning?debut=2024-06-01T08:00:00&fin=2025-06-01T18:00:00&specialitee=CAR&type=consultation
    $app->get('/praticiens/{ID-PRATICIEN}/planning', ConsulterPlanningPraticienAction::class)
    ->add(new AddHeaders);

    $app->get('/patients/{ID-PATIENT}/rdvs', ListerRendezVousPatientAction::class)
    ->add(new AddHeaders);

    return $app;
};