<?php

namespace toubeelib\core\services\rdv;


use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{

    public function getRendezVousById(string $id): RendezVousDTO;
    public function creerRendezvous(InputRendezVousDTO $r) : RendezVousDTO;
}