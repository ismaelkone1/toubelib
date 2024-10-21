<?php

namespace toubeelib\core\dto;

class GererCycleRendezVousDTO extends DTO
{

    protected string $id;
    protected string $statut;

    public function __construct(string $id, string $statut)
    {
        $this->id = $id;
        $this->statut = $statut;
    }
}