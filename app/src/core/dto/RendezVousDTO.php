<?php

namespace toubeelib\core\dto;

use toubeelib\core\domain\entities\praticien;
use toubeelib\core\domain\entities\rendezvous\RendezVous;
use toubeelib\core\dto\DTO;

class RendezVousDTO extends DTO
{
    protected string $idPatient;
    protected \DateTimeImmutable $creneau;
    protected string $praticien;
    protected string $sepcialitee;
    protected string $type;
    protected string $statut;

    public function __construct(RendezVous $rdv)
    {
        $this->idPatient = $rdv->getID();
        $this->creneau = $rdv->creneau;
        $this->praticien = $rdv->praticien;
        $this->sepcialitee = $rdv->specialitee;
        $this->type = $rdv->type;
        $this->statut = $rdv->statut;

    }
    
}