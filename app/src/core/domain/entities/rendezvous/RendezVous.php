<?php

namespace toubeelib\core\domain\entities\rendezvous;

use DateTimeImmutable;
use toubeelib\core\domain\entities\Entity;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;

class RendezVous extends Entity
{

    protected string $idPatient;

    protected \DateTimeImmutable $creneau;

    protected string $praticien;

    protected string $specialitee;

    protected string $type;

    protected string $statut;

    /**
     * @param string $idPatient
     * @param \DateTimeImmutable $creneau
     * @param Praticien $praticien
     * @param Specialite $specialitee
     */
    public function __construct(string $praticien, string $idPatient, string $specialitee,\DateTimeImmutable $creneau)
    {
        $this->idPatient = $idPatient;
        $this->creneau = $creneau;
        $this->praticien = $praticien;
        $this->specialitee = $specialitee;
    }


}