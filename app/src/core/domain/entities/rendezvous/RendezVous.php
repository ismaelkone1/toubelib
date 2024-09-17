<?php

namespace toubeelib\core\domain\entities\rendezvous;

use DateTime;
use toubeelib\core\domain\entities\Entity;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;

class RendezVous extends Entity
{

    protected string $idPatient;

    protected DateTime $creneau;

    protected Praticien $praticien;

    protected Specialite $specialitee;

    protected string $type;

    protected string $statut;

    /**
     * @param string $idPatient
     * @param \DateTime $creneau
     * @param Praticien $praticien
     * @param Specialite $specialitee
     */
    public function __construct(string $idPatient, \DateTime $creneau, Praticien $praticien, Specialite $specialitee)
    {
        $this->idPatient = $idPatient;
        $this->creneau = $creneau;
        $this->praticien = $praticien;
        $this->specialitee = $specialitee;
    }


}