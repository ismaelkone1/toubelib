<?php

namespace toubeelib\core\services\praticien;

use toubeelib\core\dto\IdPraticienDTO;
use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\SpecialiteDTO;

interface ServicePraticienInterface
{

    public function createPraticien(InputPraticienDTO $p): PraticienDTO;
    public function getPraticienById(IdPraticienDTO $id): PraticienDTO;
    public function getSpecialiteById(string $id): SpecialiteDTO;


}