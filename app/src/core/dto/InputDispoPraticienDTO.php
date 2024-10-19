<?php

namespace toubeelib\core\dto;

class InputDispoPraticienDTO extends DTO
{

    protected string $praticienId;
    protected \DateTimeImmutable $start;
    protected \DateTimeImmutable $end;

    public function __construct(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $praticienId = $this->praticienId;
        $start = $this->start;
        $end = $this->end;
    }
}