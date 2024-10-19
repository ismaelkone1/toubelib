<?php

namespace toubeelib\core\dto;

use toubeelib\core\domain\entities\rendezvous\RendezVous;

class RendezVousDTO extends DTO
{
    protected string $ID;
    protected string $idPatient;
    protected \DateTimeImmutable $creneau;
    protected string $praticien;
    protected string $specialitee;
    protected string $type;
    protected string $statut;

    public function __construct(RendezVous $rdv)
    {
        $this->ID = $rdv->getID();
        $this->idPatient = $rdv->idPatient;
        $this->creneau = $rdv->creneau;
        $this->praticien = $rdv->praticien;
        $this->specialitee = $rdv->specialitee;
        $this->type = $rdv->type;
        $this->statut = $rdv->statut;
    }

    public function getPraticien(): string
    {
        return $this->praticien;
    }

    public function getIdPatient()
    {
        return $this->idPatient;
    }

}