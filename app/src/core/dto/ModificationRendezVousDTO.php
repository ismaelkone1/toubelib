<?php

namespace toubeelib\core\dto;

use toubeelib\core\domain\entities\rendezvous\RendezVous;

class ModificationRendezVousDTO extends DTO
{
    protected string $ID;
    protected string $idPatient;
    protected string $specialitee;

    public function __construct(string $id, string $idPatient, string $specialitee)
    {
        $this->ID = $id;
        $this->idPatient = $idPatient;
        $this->specialitee = $specialitee;
    }
}