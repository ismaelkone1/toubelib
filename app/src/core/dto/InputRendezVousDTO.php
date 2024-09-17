<?php

namespace toubeelib\core\dto;

class InputRendezVousDTO extends DTO
{
    protected string $creneau;
    protected string $praticien;
    protected string $sepcialitee;
    protected string $type;
    protected string $statut;


    public function __construct(string $creneau, string $praticien, string $specialitee, string $type, string $statut)
    {
        $this->creneau = $creneau;
        $this->praticien = $praticien;
        $this->sepcialitee = $specialitee;
        $this->type = $type;
        $this->statut = $statut;

    }

}