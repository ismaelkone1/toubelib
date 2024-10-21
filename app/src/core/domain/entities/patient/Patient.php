<?php

namespace toubeelib\core\domain\entities;

use toubeelib\core\domain\entities\Entity;


class Patient extends Entity
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected string $dateNaissance;
    protected string $email;
    protected string $pass;
    protected int $role;

    public function __construct(string $nom, string $prenom, string $adresse, string $tel, string $dateNaissance, string $email)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
        $this->dateNaissance = $dateNaissance;
        $this->email = $email;
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

    public function getDateNaissance(): string
    {
        return $this->dateNaissance;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function getPassword(){
        return $this->pass;
    }

}
