<?php

namespace toubeelib\core\dto;

use DateTime;
use DateTimeImmutable;

class InputRendezVousDTO extends DTO
{
    protected string $idPatient;
    protected DateTimeImmutable $creneau;
    protected string $praticien;
    protected string $specialitee;
    protected string $type;
    protected string $statut;


    public function __construct(DateTimeImmutable $creneau, string $praticien, string $specialitee, string $type, string $statut)
    {
        $this->creneau = $creneau;
        $this->praticien = $praticien;
        $this->specialitee = $specialitee;
        $this->type = $type;
        $this->statut = $statut;

    }


    public function getIdPatient(): string 
    {
        return $this->idPatient;
    }

    public function getCreneau(): DateTimeImmutable
    {
        return $this->creneau;
    }

    public function getPraticien(): string
    {
        return $this->praticien;
    }

    public function getSpecialite(): string
    {
        return $this->specialitee;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

}