<?php

namespace toubeelib\core\services\rdv;

use Respect\Validation\Validator;
use toubeelib\core\domain\entities\rendezvous\RendezVous;
use toubeelib\core\dto\CreneauRendezVousDTO;
use toubeelib\core\dto\GererCycleRendezVousDTO;
use toubeelib\core\dto\IdPatientDTO;
use toubeelib\core\dto\IdRendezVousDTO;
use toubeelib\core\dto\InputDispoPraticienDTO;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\ModificationRendezVousDTO;
use toubeelib\core\dto\PlanningPraticienDTO;
use toubeelib\core\dto\RendezVousDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RendezVousRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInvalidDataException;


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
        $this->logger = $logger->pushHandler(new StreamHandler(__DIR__ . 'error.log', Logger::INFO));
    }


    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function getRendezVousById(IdRendezVousDTO $idRendezVousDTO): RendezVousDTO
    {
        try {
            $rendezVous = $this->rendezVousRepository->getRendezVousById($idRendezVousDTO->id);
            return new RendezVousDTO($rendezVous);
        } catch (RepositoryEntityNotFoundException $e) {
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
            throw new ServiceRendezVousInvalidDataException('Invalid RendezVous ID' . $e->getMessage());
        }

        return new RendezVousDTO($rendezVous);
    }

    /**
     * @throws RepositoryEntityNotFoundException
     * @throws ServiceRendezVousInvalidDataException
     */
    public function creerRendezVous(InputRendezVousDTO $r): RendezVousDTO
    {

        // Valider les données
        $validator = Validator::attribute('idPatient', Validator::stringType()->notEmpty())
            ->attribute('creneau', Validator::instance('DateTimeImmutable'))
            ->attribute('praticien', Validator::stringType()->notEmpty())
            ->attribute('specialitee', Validator::stringType()->notEmpty())
            ->attribute('type', Validator::stringType()->notEmpty())
            ->attribute('statut', Validator::stringType()->notEmpty());

        $r->setBusinessValidator($validator);

        try {
            $r->validate();
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ServiceRendezVousInvalidDataException('Invalid data: ' . $e->getMessage());
        }

        // Récupérer le praticien
        $le_praticien = $this->praticienRepository->getPraticienById($r->praticien);
        if (!$le_praticien) {
            throw new ServiceRendezVousInvalidDataException('Praticien non trouvé');
        }

        // Vérifier la spécialité
        $la_specialitee = $this->praticienRepository->getSpecialiteById($le_praticien->specialitee->getID());
        if (!$la_specialitee) {
            throw new ServiceRendezVousInvalidDataException('Spécialité non valide');
        }

        // Vérification de la disponibilité du créneau
        foreach ($this->rendezVousRepository->getRendezVousByPraticienEtCreneau($r->praticien, $r->creneau->modify('-30 minutes'), $r->creneau->modify('+30 minutes')) as $rdv) {
            $creneauExistant = $rdv->getCreneau();
            if ($r->creneau == $creneauExistant) {
                throw new ServiceRendezVousInvalidDataException('Le créneau est déjà réservé.');
            }
        }

        // Créer un nouveau rendez-vous
        $nrdv = new RendezVous($r->praticien, $r->idPatient, $r->specialitee, $r->creneau);
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
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        return new RendezVousDTO($rdv);
    }

    public function gererCycleRdv(GererCycleRendezVousDTO $gererCycleRendezVousDTO): RendezVousDTO
    {
        $validator = Validator::attribute('id', Validator::stringType()->notEmpty())
            ->attribute('statut', Validator::stringType()->notEmpty());

        $gererCycleRendezVousDTO->setBusinessValidator($validator);

        try {
            $gererCycleRendezVousDTO->validate();
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ServiceRendezVousInvalidDataException('Invalid data: ' . $e->getMessage());
        }

        try {
            $rdv = $this->rendezVousRepository->gererCycleRdv($gererCycleRendezVousDTO->id, $gererCycleRendezVousDTO->statut);
            return new RendezVousDTO($rdv);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException($e->getMessage());
        }
    }

    /**
     * @throws ServiceRendezVousInvalidDataException
     */
    public function listerDispoPraticien(InputDispoPraticienDTO $inputDispoPraticienDTO): array
    {

        $praticienId = $inputDispoPraticienDTO->praticienId;
        $start = $inputDispoPraticienDTO->start;
        $end = $inputDispoPraticienDTO->end;

        $validator = Validator::key('praticienId', Validator::stringType()->notEmpty())
            ->key('start', Validator::dateTime())
            ->key('end', Validator::dateTime());

        $data = [
            'praticienId' => $praticienId,
            'start' => $start,
            'end' => $end
        ];

        try {
            $validator->assert($data);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ServiceRendezVousInvalidDataException('Invalid data: ' . $e->getMessage());
        }

        try {
            $rdvs = $this->rendezVousRepository->listerDispoPraticien($praticienId, $start, $end);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        return $rdvs;
    }

    public function listerPlanningPraticien(PlanningPraticienDTO $planningPraticienDTO): array
    {
        //On vérifie que la date de début est avant la date de fin
        $validator = Validator::callback(function ($planningPraticienDTO) {
            return $planningPraticienDTO->start < $planningPraticienDTO->end;
        })->setName('Start date must be before end date');

        $planningPraticienDTO->setBusinessValidator($validator);

        try {
            $planningPraticienDTO->validate();
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new ServiceRendezVousInvalidDataException('Invalid data: ' . $e->getMessage());
        }

        try {
            $rdvs = $this->rendezVousRepository->listerPlanningPraticien($planningPraticienDTO->idPraticien, $planningPraticienDTO->start, $planningPraticienDTO->end, $planningPraticienDTO->specialitee, $planningPraticienDTO->type);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        //On retourne le planning sous la forme d'un tableau d'objets CreneauRendezVousDTO
        return array_map(function ($rdv) {
            return new CreneauRendezVousDTO($rdv['creneau']);
        }, $rdvs);
    }

    public function getRendezVousPatient(IdPatientDTO $idPatientDTO): array
    {
        try {
            $rdvs = $this->rendezVousRepository->getRendezVousPatient($idPatientDTO->idPatient);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousInvalidDataException('invalid RendezVous ID');
        }

        return $rdvs;
    }
}