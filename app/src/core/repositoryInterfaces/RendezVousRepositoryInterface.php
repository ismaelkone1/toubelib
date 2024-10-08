<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\rendezvous\RendezVous;

interface RendezVousRepositoryInterface
{

    public function save(RendezVous $rendezVous): string;
    
    public function getAll() : Array;

    public function getRendezVousById(string $id): RendezVous;

    public function modifierRendezvous(string $id, ?string $specialite, ?string $patient): RendezVous;

    public function annulerRendezvous(string $id): void;

    public function getRendezVousByPraticienAndCreneau(string $praticienId, \DateTimeImmutable $creneau): array;

    public function getRendezVousByPatient(string $patientId): array;

    public function getRendezVousByPraticienEtCreneau(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end): array;

    public function SupprimerRendezVous(string $id): void;
    
}