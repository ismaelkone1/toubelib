<?php

namespace toubeelib\core\domain\entities\praticien;

use toubeelib\core\domain\entities\Entity;
use toubeelib\core\dto\PraticienDTO;

class Praticien extends Entity
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected ?Specialite $specialite = null; // version simplifiée : une seule spécialité

    const JOURS_CONSULTATION = [ 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
    const HORAIRES_CONSULTATION = ['08:00', '18:00'];
    const DUREE_CONSULTATION = 30; // en minutes

    public function __construct(string $nom, string $prenom, string $adresse, string $tel)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
    }

    public function setSpecialite(Specialite $specialite): void
    {
        $this->specialite = $specialite;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function getTel(): string
    {
        return $this->tel;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function toDTO(): PraticienDTO
    {
        return new PraticienDTO($this);
    }

    public function getJoursConsultation(): array
    {
        return self::JOURS_CONSULTATION;
    }

    public function getHorairesConsultation(): array
    {
        return self::HORAIRES_CONSULTATION;
    }

    public function getDureeConsultation(): int
    {
        return self::DUREE_CONSULTATION;
    }
}
