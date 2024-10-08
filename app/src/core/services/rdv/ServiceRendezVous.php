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
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(RendezVousRepositoryInterface $rendezVousRepository, PraticienRepositoryInterface $praticienRepository)
    {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->praticienRepository = $praticienRepository;
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

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function modifierRendezvous(string $id, $specialitee, $patient): RendezVousDTO
    {
        try {
            $rendezVous = $this->rendezVousRepository->modifierRendezvous($id, $specialitee, $patient);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('Invalid RendezVous ID');
        }

        return new RendezVousDTO($rendezVous);
    }

    public function creerRendezVous(InputRendezVousDTO $r): RendezVousDTO
    {
        // Récupérer les données du DTO
        $idPatient = $r->getIdPatient();
        $creneau = $r->getCreneau();
        $praticienId = $r->getPraticien();
        $specialitee = $r->getSpecialite();
        $type = $r->getType();
        $statut = $r->getStatut();

        // Récupérer le praticien
        $le_praticien = $this->praticienRepository->getPraticienById($praticienId);
        if (!$le_praticien) {
            throw new ServiceRendezVousInvalidDataException('Praticien non trouvé');
        }

        // Vérifier la spécialité
        $la_specialitee = $this->praticienRepository->getSpecialiteById($le_praticien->specialitee);
        if (!$la_specialitee) {
            throw new ServiceRendezVousInvalidDataException('Spécialité non valide');
        }

        // Vérification de la disponibilité du créneau
    foreach ($this->rendezVousRepository->getRendezVousByPraticienEtCreneau($praticienId, $creneau->modify('-30 minutes'), $creneau->modify('+30 minutes')) as $rdv) {
        $creneauExistant = $rdv->getCreneau(); 
        if ($creneau == $creneauExistant) {
            throw new ServiceRendezVousInvalidDataException('Le créneau est déjà réservé.');
        }
    }

        // Créer un nouveau rendez-vous
        $nrdv = new RendezVous($praticienId, $idPatient, $specialitee, $creneau);
        $this->rendezVousRepository->save($nrdv);

        return new RendezVousDTO($nrdv);
    }

}