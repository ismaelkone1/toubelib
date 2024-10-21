<?php

namespace toubeelib\infrastructure\repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayPraticienRepository implements PraticienRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getSpecialiteById(string $id): Specialite
    {
        $stmt = $this->db->prepare('SELECT * FROM specialitee WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new RepositoryEntityNotFoundException("Specialitee $id not found");
        }

        return new Specialite($data['id'], $data['label'], $data['description']);
    }

    public function save(Praticien $praticien): string
    {
        $ID = $praticien->getId() ?: Uuid::uuid4()->toString();
        $praticien->setID($ID);

        $stmt = $this->db->prepare('
            INSERT INTO praticien (id, nom, prenom, tel, adresse, specialitee_id)
            VALUES (:id, :nom, :prenom, :tel, :adresse, :specialitee_id)
            ON CONFLICT (id) DO UPDATE
            SET nom = EXCLUDED.nom, prenom = EXCLUDED.prenom, tel = EXCLUDED.tel, adresse = EXCLUDED.adresse, specialitee_id = EXCLUDED.specialitee_id
        ');

        $stmt->execute([
            ':id' => $ID,
            ':nom' => $praticien->getNom(),
            ':prenom' => $praticien->getPrenom(),
            ':tel' => $praticien->getTel(),
            ':adresse' => $praticien->getAdresse(),
            ':specialitee_id' => $praticien->getSpecialite()->getId()
        ]);

        return $ID;
    }

    public function getPraticienById(string $id): Praticien
    {
        $stmt = $this->db->prepare('SELECT * FROM praticien WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new RepositoryEntityNotFoundException("Praticien $id not found");
        }

        return $this->mapToPraticien($data);
    }

    private function mapToPraticien(array $data): Praticien
    {
        $praticien = new Praticien(
            $data['nom'],
            $data['prenom'],
            $data['adresse'],
            $data['tel']
        );
        $praticien->setID($data['id']);
        
        // Récupérer la spécialité
        $specialite = $this->getSpecialiteById($data['specialitee_id']);
        $praticien->setSpecialite($specialite);

        return $praticien;
    }
}
