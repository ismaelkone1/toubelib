<?php

namespace toubeelib\core\services\rdv;


use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{

    public function getRendezVousById(string $id): RendezVousDTO;
    public function creerRendezVous(InputRendezVousDTO $r) : RendezVousDTO;

    public function modifierRendezvous(string $id, ?string $specialite, ?string $patient): RendezVousDTO;
    public function annulerRendezvous(string $id): RendezVousDTO;
}