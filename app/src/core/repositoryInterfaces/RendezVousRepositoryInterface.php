<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\rendezvous\RendezVous;

interface RendezVousRepositoryInterface
{
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function save(RendezVous $rendezVous): string;
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function getAll() : array;
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function getRendezVousById(string $id): RendezVous;
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function modifierRendezvous(string $id, ?string $specialite, ?string $patient): RendezVous;
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function annulerRendezvous(string $id): RendezVous;
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function getRendezVousByPraticienAndCreneau(string $praticienId, \DateTimeImmutable $creneau): array;
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function getRendezVousByPatient(string $patientId): array;
    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function getRendezVousByPraticienEtCreneau(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end): array;

    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function listerDispoPraticien(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end): array;
}