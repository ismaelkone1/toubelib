<?php

namespace toubeelib\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\rendezvous\RendezVous;
use toubeelib\core\repositoryInterfaces\RendezVousRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayRdvRepository implements RendezVousRepositoryInterface
{
    private array $rdvs = [];

    public function __construct() {
            $r1 = new RendezVous('p1', 'pa1', 'A', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:00') );
            $r1->setID('r1');
            $r2 = new RendezVous('p1', 'pa1', 'A', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 10:00'));
            $r2->setID('r2');
            $r3 = new RendezVous('p2', 'pa1', 'A', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:30'));
            $r3->setID('r3');

        $this->rdvs  = ['r1'=> $r1, 'r2'=>$r2, 'r3'=> $r3 ];
    }


    public function save(RendezVous $rendezVous): string
    {
        // TODO: Implement save() method.
    }

    public function modifierRendezvous(string $id, ?string $specialite, ?string $patient): RendezVous
    {
        $rdv = $this->getRendezVousById($id);

        //On compare la spécialité du rdv avec celle donnée en paramètre
        if($specialite != $rdv->specialitee && $specialite != null) {
            $rdv->setSpecialite($specialite);
        }
        if($patient != $rdv->idPatient && $patient != null) {
            $rdv->setPatient($patient);
        }
        return $rdv;
    }

    public function getRendezVousById(string $id): RendezVous
    {
        return $this->rdvs[$id] ?? throw new RepositoryEntityNotFoundException("RendezVous $id not found");
    }
}