<?php

use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\services\rdv\ServiceRendezVous;
use toubeelib\core\services\rdv\ServiceRendezVousInvalidDataException;


$service = new ServiceRendezVous(new \toubeelib\infrastructure\repositories\ArrayRdvRepository());

try {
    $re33 = $service->getRendezVousById('r1');
} catch (ServiceRendezVousInvalidDataException $e) {
    echo 'Exception dans la récupération d\'un rendez-vous :' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}

var_dump($re33); // Affiche les détails du rendez-vous récupéré