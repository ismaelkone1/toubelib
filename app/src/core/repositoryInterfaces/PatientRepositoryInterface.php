<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\patient\Patient;

interface PatientRepositoryInterface
{
    /**
     * Trouver un patient par son email.
     *
     * @param string $email
     * @return Patient|null
     */
    public function findByEmail(string $email): ?Patient;

    /**
     * Trouver un patient par son ID.
     *
     * @param string $id
     * @return Patient|null
     */
    public function findById(string $id): ?Patient;

    /**
     * Enregistrer un patient dans le repository.
     *
     * @param Patient $patient
     * @return string ID du patient
     */
    public function save(Patient $patient): string;

    /**
     * Supprimer un patient à partir de son ID.
     *
     * @param string $id
     * @throws RepositoryEntityNotFoundException si le patient n'est pas trouvé
     * @return void
     */
    public function delete(string $id): void;

    /**
     * Obtenir tous les patients du repository.
     *
     * @return array Liste de patients
     */
    public function getAll(): array;


}
