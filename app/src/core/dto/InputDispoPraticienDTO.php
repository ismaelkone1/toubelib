<?php

namespace toubeelib\core\dto;

class InputDispoPraticienDTO extends DTO
{

    protected string $praticienId;
    protected \DateTimeImmutable $start;
    protected \DateTimeImmutable $end;

    public function __construct(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $this->praticienId = $praticienId;
        $this->start = $start;
        $this->end = $end;
    }
}