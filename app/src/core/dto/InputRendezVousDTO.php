<?php

namespace toubeelib\core\dto;

use DateTimeImmutable;
use InvalidArgumentException;
use Respect\Validation\Validator as v;

class InputRendezVousDTO extends DTO
{
    protected string $idPatient;
    protected DateTimeImmutable $creneau;
    protected string $praticien;
    protected string $specialitee;
    protected string $type;
    protected string $statut;

    public function __construct(string $idPatient, DateTimeImmutable $creneau, string $praticien, string $specialitee, string $type, string $statut)
    {
        $this->setIdPatient($idPatient);
        $this->setCreneau($creneau);
        $this->setPraticien($praticien);
        $this->setSpecialite($specialitee);
        $this->setType($type);
        $this->setStatut($statut);
    }

    public function getIdPatient(): string
    {
        return $this->idPatient;
    }

    public function setIdPatient(string $idPatient): void
    {
        if (!v::stringType()->notEmpty()->validate($idPatient)) {
            throw new InvalidArgumentException('Invalid patient ID');
        }
        $this->idPatient = $idPatient;
    }

    public function getCreneau(): DateTimeImmutable
    {
        return $this->creneau;
    }

    public function setCreneau(DateTimeImmutable $creneau): void
    {
        $this->creneau = $creneau;
    }

    public function getPraticien(): string
    {
        return $this->praticien;
    }

    public function setPraticien(string $praticien): void
    {
        if (!v::stringType()->notEmpty()->validate($praticien)) {
            throw new InvalidArgumentException('Invalid practitioner ID');
        }
        $this->praticien = $praticien;
    }

    public function getSpecialite(): string
    {
        return $this->specialitee;
    }

    public function setSpecialite(string $specialitee): void
    {
        if (!v::stringType()->notEmpty()->validate($specialitee)) {
            throw new InvalidArgumentException('Invalid specialty');
        }
        $this->specialitee = $specialitee;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (!v::stringType()->notEmpty()->validate($type)) {
            throw new InvalidArgumentException('Invalid type');
        }
        $this->type = $type;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        if (!v::stringType()->notEmpty()->validate($statut)) {
            throw new InvalidArgumentException('Invalid status');
        }
        $this->statut = $statut;
    }
}