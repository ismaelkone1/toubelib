<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\domain\entities\rendezvous\RendezVous;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RendezVousRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ServiceRendezVous implements ServiceRendezVousInterface
{

    private RendezVousRepositoryInterface $rendezVousRepository;

    public function __construct(RendezVousRepositoryInterface $rendezVousRepository)
    {
        $this->rendezVousRepository = $rendezVousRepository;
    }

    public function getRendezVousById(string $id): RendezVousDTO
    {
        try {
            $rendezVous = $this->rendezVousRepository->getRendezVousById($id);
            return new RendezVousDTO($rendezVous);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }
    }

    public function creerRendezvous(InputRendezVousDTO $r): RendezVousDTO
    {
        try {
            //Etape 1 : vérification d l'existence du praticien 
            $praticien = $this->praticienRepository->getPraticienById($r->idPraticien);
            if (!$praticien) {
                throw new ServiceRendezVousInvalidDataException('Praticien non trouve');
            }
            //La specilatite du rdv fait partie de celles du praticien
            $specialite = $this->praticienRepository->getSpecialiteById($r->sepcialitee);
            if ($specialite != $r->sepcialitee) {
                throw new ServiceRendezVousInvalidDataException('Specialitee non valide');
            }

        } catch (\Throwable $th) {
            //throw $th;²
         }
    }
}