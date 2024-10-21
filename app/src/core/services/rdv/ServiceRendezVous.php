<?php

namespace toubeelib\core\services\rdv;

use Respect\Validation\Validator;
use toubeelib\core\domain\entities\rendezvous\RendezVous;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\ModificationRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RendezVousRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class ServiceRendezVous implements ServiceRendezVousInterface
{

    private RendezVousRepositoryInterface $rendezVousRepository;
    private PraticienRepositoryInterface $praticienRepository;

    private $logger;

    public function __construct(RendezVousRepositoryInterface $rendezVousRepository, PraticienRepositoryInterface $praticienRepository)
    {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->praticienRepository = $praticienRepository;
        $logger = new Logger('my_logger');
        $this->logger = $logger->pushHandler(new StreamHandler(__DIR__.'error.log', Logger::INFO));
    }


    /**
     * @throws ServiceRendezVousInvalidDataException
     */
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
    public function modifierRendezvous(ModificationRendezVousDTO $modificationRendezVousDTO): RendezVousDTO
    {

        //On valide le fait que le patient ou bien la spécialité soit renseigné
        $modifRdvValidator = Validator::anyOf(
            Validator::attribute('specialitee', Validator::stringType()->notEmpty()),
            Validator::attribute('idPatient', Validator::stringType()->notEmpty())
        );

        $modificationRendezVousDTO->setBusinessValidator($modifRdvValidator);

        try {
            $modificationRendezVousDTO->validate();
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            $this->logger->info('Invalid place data');
            throw new ServiceRendezVousInvalidDataException('Invalid place data: ' . $e->getMessage());
        }

        //TODO: voir si on transmet le DTO ou les paramètres
        try {
            $rendezVous = $this->rendezVousRepository->modifierRendezvous($modificationRendezVousDTO->id, $modificationRendezVousDTO->specialitee, $modificationRendezVousDTO->idPatient);
            $this->logger->info('Modification de rendez-vous bien enregistrée', [
                'id' => $modificationRendezVousDTO->id,
                'specialitee' => $modificationRendezVousDTO->specialitee,
                'idPatient' => $modificationRendezVousDTO->idPatient,
            ]);
        } catch (RepositoryEntityNotFoundException $e) {
            $this->logger->error('Modification de rendez-vous échouée', [
                'error' => $e->getMessage(),
            ]);
            throw new ServiceRendezVousInvalidDataException('Invalid RendezVous ID');
        }

        return new RendezVousDTO($rendezVous);
    }

    /**
     * @throws RepositoryEntityNotFoundException
     * @throws ServiceRendezVousInvalidDataException
     */
    public function creerRendezVous(InputRendezVousDTO $r): RendezVousDTO
    {
        // Récupérer les données du DTO
        $idPatient = $r->getIdPatient();
        $creneau = $r->getCreneau();
        $praticienId = $r->getPraticien();
        $specialitee = $r->getSpecialite();
        $type = $r->getType();
        $statut = $r->getStatut();

        // Valider les données
        $validator = Validator::key('idPatient', Validator::stringType()->notEmpty())
            ->key('creneau', Validator::dateTime())
            ->key('praticienId', Validator::stringType()->notEmpty())
            ->key('specialitee', Validator::stringType()->notEmpty())
            ->key('type', Validator::stringType()->notEmpty())
            ->key('statut', Validator::stringType()->notEmpty());

        $data = [
            'idPatient' => $idPatient,
            'creneau' => $creneau,
            'praticienId' => $praticienId,
            'specialitee' => $specialitee,
            'type' => $type,
            'statut' => $statut
        ];

        try {
            $validator->assert($data);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ServiceRendezVousInvalidDataException('Invalid data: ' . $e->getMessages());
        }

        // Récupérer le praticien
        $le_praticien = $this->praticienRepository->getPraticienById($praticienId);
        if (!$le_praticien) {
            throw new ServiceRendezVousInvalidDataException('Praticien non trouvé');
        }

        // Vérifier la spécialité
        $la_specialitee = $this->praticienRepository->getSpecialiteById($le_praticien->specialitee->getID());
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

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function annulerRendezvous(string $id): RendezVousDTO
    {
        try {
            $rdv = $this->rendezVousRepository->annulerRendezvous($id);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        return new RendezVousDTO($rdv);
    }

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function setStatutHonore(string $id): RendezVousDTO
    {
        try {
            $rdv = $this->rendezVousRepository->setStatutHonore($id);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        return new RendezVousDTO($rdv);
    }

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function setStatutPaye(string $id): RendezVousDTO{
        try {
            $rdv = $this->rendezVousRepository->setStatutPaye($id);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        return new RendezVousDTO($rdv);
    }

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function setStatutNonHonore(string $id): RendezVousDTO
    {
        try {
            $rdv = $this->rendezVousRepository->setStatutNonHonore($id);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        return new RendezVousDTO($rdv);
    }

}