<?php

namespace toubeelib\core\domain\entities\rendezvous;

use toubeelib\core\domain\entities\Entity;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;

class RendezVous extends Entity
{

    protected string $idPatient;

    protected \DateTime $creneau;

    protected Praticien $praticien;

    protected Specialite $specialitee;


}