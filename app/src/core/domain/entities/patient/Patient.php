<?php

namespace toubeelib\core\domain\entities\patient;
use toubeelib\core\domain\entities\Entity;

class Patient extends Entity
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected string $email;
    protected string $pass;
    protected int $role;

    public function __construct(string $nom, string $prenom, string $adresse, string $tel, string $email, string $pass)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
        $this->email = $email;
        $this->pass = $pass;
        $this->role = 1;
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
