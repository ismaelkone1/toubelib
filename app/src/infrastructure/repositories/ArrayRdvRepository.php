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
        $ID = Uuid::uuid4()->toString();
        $rendezVous->setID($ID);
        $this->rdvs[$ID] = $rendezVous;
        return $ID;
    }

    public function getAll(): array
    {
        return array_values($this->rdvs); // Renvoie un tableau contenant tous les rendez-vous
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

    public function getRendezVousByPraticienAndCreneau(string $praticienId, \DateTimeImmutable $creneau): array
    {
    return array_filter($this->rdvs, function($rdv) use ($praticienId, $creneau) {
        return $rdv->getPraticienId() === $praticienId && $rdv->getCreneau() == $creneau;
    });
    }

    public function getRendezVousByPatient(string $patientId): array
    {
        return array_filter($this->rdvs, function($rdv) use ($patientId) {
            return $rdv->getPatientId() === $patientId;
        });
    }

    public function getRendezVousByPraticienEtCreneau(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        return array_filter($this->rdvs, function($rdv) use ($praticienId, $start, $end) {
            $creneau = $rdv->getCreneau();
            return $rdv->getPraticienId() === $praticienId && $creneau >= $start && $creneau <= $end;
        });
    }

    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function annulerRendezvous(string $id): RendezVous
    {
        $rdv = $this->getRendezVousById($id);
        $rdv->setStatut(RendezVous::ANNULE);
        return $rdv;
    }


    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function setStatutHonore(string $id): RendezVous
    {
        $rdv = $this->getRendezVousById($id);
        $rdv->setStatut(RendezVous::HONORE);
        return $rdv;
    }


    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function setStatutPaye(string $id): RendezVous
    {
        $rdv = $this->getRendezVousById($id);
        $rdv->setStatut(RendezVous::PAYE);
        return $rdv;
    }

    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function setStatutNonHonore(string $id): RendezVous
    {
        $rdv = $this->getRendezVousById($id);
        $rdv->setStatut(RendezVous::NON_HONORE);
        return $rdv;
    }

    public function listerDispoPraticien()
    {

    }
}