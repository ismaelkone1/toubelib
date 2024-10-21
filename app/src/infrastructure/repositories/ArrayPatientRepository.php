<?php

namespace toubeelib\infrastructure\repositories;

use toubeelib\core\domain\entities\Patient;
use toubeelib\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use Ramsey\Uuid\Uuid;

class ArrayPatientRepository implements PatientRepositoryInterface
{
    private array $patients = [];

    public function __construct(array $initialPatients = [])
    {
        // Si des patients sont fournis au départ, on les ajoute au tableau.
        foreach ($initialPatients as $patient) {
            $this->patients[$patient->getId()] = $patient;
        }
    }

    public function findByEmail(string $email): ?Patient
    {
        foreach ($this->patients as $patient) {
            if ($patient->getEmail() === $email) {
                return $patient;
            }
        }

        return null; // Pas de patient trouvé
    }

    public function findById(string $id): ?Patient
    {
        return $this->patients[$id] ?? null; // Retourne null si le patient n'est pas trouvé
    }

    public function save(Patient $patient): string
    {
        $id = $patient->getId() ?: Uuid::uuid4()->toString();
        $patient->setId($id); // Assigne un ID si non existant

        // Ajout ou mise à jour du patient dans le tableau
        $this->patients[$id] = $patient;

        return $id;
    }

    public function delete(string $id): void
    {
        if (!isset($this->patients[$id])) {
            throw new RepositoryEntityNotFoundException("Patient $id not found");
        }

        unset($this->patients[$id]);
    }

    /**
     * Récupère tous les patients du repository
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->patients;
    }
}
