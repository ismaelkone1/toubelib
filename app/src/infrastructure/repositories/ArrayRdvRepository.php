<?php

namespace toubeelib\infrastructure\repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\rendezvous\RendezVous;
use toubeelib\core\repositoryInterfaces\RendezVousRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayRdvRepository implements RendezVousRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(RendezVous $rendezVous): string
    {
        $id = Uuid::uuid4()->toString();
        $rendezVous->setID($id);

        $stmt = $this->db->prepare('
            INSERT INTO rdv (id, id_praticien, id_patient, id_spe, type, statut, creneau) 
            VALUES (:id, :id_praticien, :id_patient, :id_spe, :type, :statut, :creneau)
        ');

        $stmt->execute([
            ':id' => $id,
            ':id_praticien' => $rendezVous->getPraticienId(),
            ':id_patient' => $rendezVous->getPatientId(),
            ':id_spe' => $rendezVous->getSpecialiteeId(),
            ':type' => $rendezVous->getType(),
            ':statut' => $rendezVous->getStatut(),
            ':creneau' => $rendezVous->getCreneau()->format('Y-m-d H:i:s')
        ]);

        return $id;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM rdv');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($data) {
            return $this->mapToRendezVous($data);
        }, $results);
    }

    public function modifierRendezvous(string $id, ?string $specialite, ?string $patient): RendezVous
    {
        $rdv = $this->getRendezVousById($id);

        $updateFields = [];
        $params = [':id' => $id];

        if ($specialite !== null && $specialite !== $rdv->specialitee) {
            $updateFields[] = 'id_spe = :specialite';
            $params[':specialite'] = $specialite;
        }

        if ($patient !== null && $patient !== $rdv->idPatient) {
            $updateFields[] = 'id_patient = :patient';
            $params[':patient'] = $patient;
        }

        if (!empty($updateFields)) {
            $sql = 'UPDATE rdv SET ' . implode(', ', $updateFields) . ' WHERE id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        }

        return $this->getRendezVousById($id);
    }

    public function getRendezVousById(string $id): RendezVous
    {
        $stmt = $this->db->prepare('SELECT * FROM rdv WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            throw new RepositoryEntityNotFoundException("RendezVous $id not found");
        }

        return $this->mapToRendezVous($data);
    }

    public function getRendezVousByPraticienAndCreneau(string $praticienId, \DateTimeImmutable $creneau): array
    {
        $stmt = $this->db->prepare('SELECT * FROM rdv WHERE id_praticien = :id_praticien AND creneau = :creneau');
        $stmt->execute([
            ':id_praticien' => $praticienId,
            ':creneau' => $creneau->format('Y-m-d H:i:s')
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToRendezVous'], $results);
    }

    public function getRendezVousByPatient(string $patientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM rdv WHERE id_patient = :id_patient');
        $stmt->execute([':id_patient' => $patientId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToRendezVous'], $results);
    }

    public function getRendezVousByPraticienEtCreneau(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM rdv 
            WHERE id_praticien = :id_praticien 
            AND creneau BETWEEN :start AND :end
        ');
        $stmt->execute([
            ':id_praticien' => $praticienId,
            ':start' => $start->format('Y-m-d H:i:s'),
            ':end' => $end->format('Y-m-d H:i:s')
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToRendezVous'], $results);
    }

    public function annulerRendezvous(string $id): RendezVous
    {
        return $this->setStatut($id, RendezVous::ANNULE);
    }

    public function setStatutHonore(string $id): RendezVous
    {
        return $this->setStatut($id, RendezVous::HONORE);
    }

    public function setStatutPaye(string $id): RendezVous
    {
        return $this->setStatut($id, RendezVous::PAYE);
    }

    public function setStatutNonHonore(string $id): RendezVous
    {
        return $this->setStatut($id, RendezVous::NON_HONORE);
    }

    private function setStatut(string $id, string $statut): RendezVous
    {
        $stmt = $this->db->prepare('UPDATE rdv SET statut = :statut WHERE id = :id');
        $stmt->execute([':statut' => $statut, ':id' => $id]);

        return $this->getRendezVousById($id);
    }

    private function mapToRendezVous(array $data): RendezVous
    {
        //On v

        $rdv = new RendezVous(
            $data['id_praticien'],
            $data['id_patient'],
            $data['id_spe'],
            new \DateTimeImmutable($data['creneau'])
        );
        $rdv->setID($data['id']);
        $rdv->setStatut($data['statut']);
        return $rdv;
    }
}
