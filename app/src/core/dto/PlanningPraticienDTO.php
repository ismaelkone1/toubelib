<?php

namespace toubeelib\core\dto;

use DateTimeImmutable;

class PlanningPraticienDTO extends DTO
{

    protected string $idPraticien;
    protected DateTimeImmutable $start;
    protected DateTimeImmutable $end;
    protected string $specialitee;
    protected string $type;

    public function __construct(string $idPraticien, DateTimeImmutable $start, DateTimeImmutable $end, string $specialitee, string $type)
    {
        $this->idPraticien = $idPraticien;
        $this->start = $start;
        $this->end = $end;
        $this->specialitee = $specialitee;
        $this->type = $type;
    }

}