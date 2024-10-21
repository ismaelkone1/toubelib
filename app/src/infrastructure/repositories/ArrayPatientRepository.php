<?php

namespace toubeelib\infrastructure\repositories;

use PDO;
use toubeelib\core\domain\entities\patient\Patient;
use toubeelib\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use Ramsey\Uuid\Uuid;

class ArrayPatientRepository implements PatientRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?Patient
    {
        $stmt = $this->db->prepare('SELECT * FROM patient WHERE email = :email');
        $stmt->execute([':email' => $email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToPatient($data);
    }

    public function findById(string $id): ?Patient
    {
        $stmt = $this->db->prepare('SELECT * FROM patient WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToPatient($data);
    }

    public function save(Patient $patient): string
    {
        $id = $patient->getId() ?: Uuid::uuid4()->toString();
        $patient->setId($id);

        $stmt = $this->db->prepare('
            INSERT INTO patient (id, nom, prenom, email, tel, adresse)
            VALUES (:id, :nom, :prenom, :email, :tel, :adresse)
            ON CONFLICT (id) DO UPDATE
            SET nom = EXCLUDED.nom, prenom = EXCLUDED.prenom, email = EXCLUDED.email, tel = EXCLUDED.tel, adresse = EXCLUDED.adresse
        ');

        $stmt->execute([
            ':id' => $id,
            ':nom' => $patient->getNom(),
            ':prenom' => $patient->getPrenom(),
            ':email' => $patient->getEmail(),
            ':tel' => $patient->getTel(),
            ':adresse' => $patient->getAdresse()
        ]);

        return $id;
    }

    public function delete(string $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM patient WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw new RepositoryEntityNotFoundException("Patient $id not found");
        }
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM patient');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => $this->mapToPatient($data), $data);
    }

    private function mapToPatient(array $data): Patient
    {
        $patient = new Patient(
            $data['nom'],
            $data['prenom'],
            $data['adresse'],
            $data['tel'],
            $data['email'],
            $data['password']
        );
        $patient->setId($data['id']);

        return $patient;
    }
}