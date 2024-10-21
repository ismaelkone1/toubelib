<?php

namespace toubeelib\infrastructure\repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\patient\praticien\Praticien;
use toubeelib\core\domain\entities\patient\rendezvous\RendezVous;
use toubeelib\core\repositoryInterfaces\RendezVousRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayRdvRepository implements RendezVousRepositoryInterface
{
    private PDO $rdvDb;

    private PDO $patientDb;

    private PDO $praticienDb;

    public function __construct(PDO $rdvDb, PDO $patientDb, PDO $praticienDb)
    {
        $this->rdvDb = $rdvDb;
        $this->patientDb = $patientDb;
        $this->praticienDb = $praticienDb;
    }

    public function save(RendezVous $rendezVous): string
    {
        $id = Uuid::uuid4()->toString();
        $rendezVous->setID($id);

        $stmt = $this->rdvDb->prepare('
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
        $stmt = $this->rdvDb->query('SELECT * FROM rdv');
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

            $stmt = $this->praticienDb->prepare('SELECT * FROM specialitee WHERE id = :id');
            $stmt->execute([':id' => $specialite]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                throw new RepositoryEntityNotFoundException("Specialite $specialite not found");
            }

            $updateFields[] = 'id_spe = :specialite';
            $params[':specialite'] = $specialite;
        }

        if ($patient !== null && $patient !== $rdv->idPatient) {

            $stmt2 = $this->patientDb->prepare('SELECT * FROM patient WHERE id = :id');
            $stmt2->execute([':id' => $patient]);

            $data = $stmt2->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                throw new RepositoryEntityNotFoundException("Patient $patient not found");
            }

            $updateFields[] = 'id_patient = :patient';
            $params[':patient'] = $patient;
        }

        if (!empty($updateFields)) {
            $sql = 'UPDATE rdv SET ' . implode(', ', $updateFields) . ' WHERE id = :id';
            $stmt3 = $this->rdvDb->prepare($sql);
            $stmt3->execute($params);
        }

        return $this->getRendezVousById($id);
    }

    public function getRendezVousById(string $id): RendezVous
    {
        $stmt = $this->rdvDb->prepare('SELECT * FROM rdv WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            throw new RepositoryEntityNotFoundException("RendezVous $id not found");
        }

        return $this->mapToRendezVous($data);
    }

    public function getRendezVousByPraticienAndCreneau(string $praticienId, \DateTimeImmutable $creneau): array
    {
        $stmt = $this->rdvDb->prepare('SELECT * FROM rdv WHERE id_praticien = :id_praticien AND creneau = :creneau');
        $stmt->execute([
            ':id_praticien' => $praticienId,
            ':creneau' => $creneau->format('Y-m-d H:i:s')
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToRendezVous'], $results);
    }

    public function getRendezVousByPatient(string $patientId): array
    {
        $stmt = $this->rdvDb->prepare('SELECT * FROM rdv WHERE id_patient = :id_patient');
        $stmt->execute([':id_patient' => $patientId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToRendezVous'], $results);
    }

    public function getRendezVousByPraticienEtCreneau(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $stmt = $this->rdvDb->prepare('
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

    public function gererCycleRdv(string $id, string $statut): RendezVous
    {

        switch ($statut) {
            case RendezVous::HONORE:
                return $this->setStatutHonore($id);
            case RendezVous::PAYE:
                return $this->setStatutPaye($id);
            case RendezVous::NON_HONORE:
                return $this->setStatutNonHonore($id);
            default:
                throw new RepositoryEntityNotFoundException("Statut x$statut not found");
        }
    }

    private function setStatutHonore(string $id): RendezVous
    {
        return $this->setStatut($id, RendezVous::HONORE);
    }

    private function setStatutPaye(string $id): RendezVous
    {
        return $this->setStatut($id, RendezVous::PAYE);
    }

    private function setStatutNonHonore(string $id): RendezVous
    {
        return $this->setStatut($id, RendezVous::NON_HONORE);
    }

    private function setStatut(string $id, string $statut): RendezVous
    {
        $stmt = $this->rdvDb->prepare('UPDATE rdv SET statut = :statut WHERE id = :id');
        $stmt->execute([':statut' => $statut, ':id' => $id]);

        return $this->getRendezVousById($id);
    }

    private function mapToRendezVous(array $data): RendezVous
    {

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

    public function listerDispoPraticien(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        // Retrieve the Praticien entity
        $stmt = $this->praticienDb->prepare('SELECT * FROM praticien WHERE id = :id');
        $stmt->execute([':id' => $praticienId]);
        $praticienData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$praticienData) {
            throw new RepositoryEntityNotFoundException("Praticien $praticienId not found");
        }

        $joursConsultation = Praticien::JOURS_CONSULTATION;
        $horairesConsultation = Praticien::HORAIRES_CONSULTATION;
        $dureeConsultation = Praticien::DUREE_CONSULTATION;

        // Fetch existing appointments
        $stmt = $this->rdvDb->prepare('
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


        $creneaux = [];
        $creneau = $start;
        while ($creneau < $end) {
            $dayOfWeek = strtolower($creneau->format('l'));
            $hour = $creneau->format('H:i');

            //Si le jour de la semaine est un jour de consultation et que l'heure est comprise dans les horaires de consultation
            if (in_array($dayOfWeek, $joursConsultation) && $hour >= $horairesConsultation[0] && $hour < $horairesConsultation[1]) {
                $isAvailable = true;
                foreach ($results as $result) {
                    //Si il n'y a aucun rendez vous dureeConsultation avant ou après le créneau
                    if ( $creneau->modify("-{$dureeConsultation} minutes") < new \DateTimeImmutable($result['creneau']) && $creneau->modify("+{$dureeConsultation} minutes") > new \DateTimeImmutable($result['creneau'])) {
                        $isAvailable = false;
                        break;
                    }
                }
                if ($isAvailable) {
                    $creneaux[] = $creneau;
                }
            }
            $creneau = $creneau->modify("+{$dureeConsultation} minutes");
        }

        return $creneaux;
    }

    /**
     * afficher le planning d’un praticien sur une période donnée (date de début, date de fin) en
     * précisant la spécialité concernée et le type de consultation (présentiel, téléconsultation),
     */
    public function listerPlanningPraticien(string $praticienId, \DateTimeImmutable $start, \DateTimeImmutable $end, string $specialite, string $type): array {

        //On récupere d'abord le label de la spécialité
        $stmt = $this->praticienDb->prepare('SELECT * FROM specialitee WHERE label = :label');
        $stmt->execute([':label' => $specialite]);
        $specialiteData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$specialiteData) {
            throw new RepositoryEntityNotFoundException("Specialite $specialite not found");
        }

        $specialiteId = $specialiteData['id'];


        //On fais une requête pour récupérer les rendez-vous du praticien avec la spécialité et le type de consultation
        $stmt = $this->rdvDb->prepare('SELECT * FROM rdv WHERE id_praticien = :id_praticien AND creneau BETWEEN :start AND :end AND id_spe = :id_spe AND type = :type');


        $stmt->execute([
            ':id_praticien' => $praticienId,
            ':start' => $start->format('Y-m-d H:i:s'),
            ':end' => $end->format('Y-m-d H:i:s'),
            ':id_spe' => $specialiteId,
            ':type' => $type
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    public function getRendezVousPatient(string $patientId): array
    {
        $stmt = $this->rdvDb->prepare('SELECT * FROM rdv WHERE id_patient = :id_patient');
        $stmt->execute([':id_patient' => $patientId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}