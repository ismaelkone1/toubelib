<?php

namespace toubeelib\core\domain\entities\patient\rendezvous;

use DateTimeImmutable;
use toubeelib\core\domain\entities\patient\Entity;
use toubeelib\core\domain\entities\patient\praticien\Praticien;
use toubeelib\core\domain\entities\patient\praticien\Specialite;

class RendezVous extends Entity
{

    protected string $idPatient;

    protected \DateTimeImmutable $creneau;

    protected string $praticien;

    protected string $specialitee;

    protected string $type;

    /**
     * @var string
     * 0: prévu
     * 1: annulé
     * 2: honoré
     * 3: payé
     * 4: non honoré
     */
    protected string $statut;

    const PREVU = '0';
    const ANNULE = '1';
    const HONORE = '2';
    const PAYE = '3';
    const NON_HONORE = '4';

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
        $this->statut = self::PREVU;
        $this->type = 'presentiel';
    }

    public function setSpecialite(?string $specialite)
    {
        $this->specialitee = $specialite;
    }

    public function setPatient(?string $patient)
    {
        $this->idPatient = $patient;
    }


    public function setStatut(?string $statut)
    {
        $this->statut = $statut;
    }

    public function getPraticienId(){
        return $this->praticien;
    }

    public function getPatientId(){
        return $this->idPatient;
    }

    public function getSpecialiteeId(){
        return $this->specialitee;
    }

    public function getStatut(){
        return $this->statut;
    }

    public function getCreneau(){
        return $this->creneau;
    }
    public function getType(){
        return $this->type;
    }
}