<?php

namespace toubeelib\core\services\rdv;


use toubeelib\core\dto\IdRendezVousDTO;
use toubeelib\core\dto\InputDispoPraticienDTO;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\ModificationRendezVousDTO;
use toubeelib\core\dto\PlanningPraticienDTO;
use toubeelib\core\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{

    public function getRendezVousById(IdRendezVousDTO $idRendezVousDTO): RendezVousDTO;
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

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function listerPlanningPraticien(PlanningPraticienDTO $planningPraticienDTO): array;
}