<?php

namespace toubeelib\core\services\rdv;


use toubeelib\core\dto\InputDispoPraticienDTO;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\ModificationRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{

    public function getRendezVousById(string $id): RendezVousDTO;
    public function creerRendezVous(InputRendezVousDTO $r) : RendezVousDTO;

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function modifierRendezvous(ModificationRendezVousDTO $modificationRendezVousDTO): RendezVousDTO;
    public function annulerRendezvous(string $id): RendezVousDTO;

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function listerDispoPraticien(InputDispoPraticienDTO $inputDispoPraticienDTO): array;
}