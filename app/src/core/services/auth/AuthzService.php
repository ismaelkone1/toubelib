<?php

namespace toubeelib\core\services;

use toubeelib\core\dto\AuthDTO;

class AuthzService
{
    const ROLE_PRATICIEN = 10;
    const ROLE_ADMIN = 5;

    public function isPraticienOrAdmin(AuthDTO $authDTO): bool
    {
        return in_array($authDTO->getRole(), [self::ROLE_PRATICIEN, self::ROLE_ADMIN]);
    }

    public function canAccessPraticienProfile(AuthDTO $authDTO, string $praticienId): bool
    {
        return $authDTO->getRole() === self::ROLE_PRATICIEN && $authDTO->getId() === $praticienId;
    }
}
