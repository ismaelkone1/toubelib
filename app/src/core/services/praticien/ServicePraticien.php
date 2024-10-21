<?php

namespace toubeelib\core\services\praticien;

use Respect\Validation\Exceptions\NestedValidationException;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\dto\IdPraticienDTO;
use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\SpecialiteDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\services\auth\auth\praticien\ServicePraticienInterface;
use toubeelib\core\services\auth\auth\praticien\ServicePraticienInvalidDataException;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    public function createPraticien(InputPraticienDTO $p): PraticienDTO
    {
        // TODO : valider les données et créer l'entité
        return new PraticienDTO($praticien);
    }

    public function getPraticienById(IdPraticienDTO $idPraticienDTO): PraticienDTO
    {
        try {
            $praticien = $this->praticienRepository->getPraticienById($idPraticienDTO->id);
            return new PraticienDTO($praticien);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Praticien ID');
        }
    }

    public function getAllPraticiens(): array
    {
        $praticiens = $this->praticienRepository->getAllPraticiens();
        return array_map(fn(Praticien $praticien) => $praticien->toDTO(), $praticiens);
    }

    public function getSpecialiteById(string $id): SpecialiteDTO
    {
        try {
            $specialite = $this->praticienRepository->getSpecialiteById($id);
            return $specialite->toDTO();
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Specialite ID');
        }
    }
}