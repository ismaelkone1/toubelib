<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\rendezvous\RendezVous;

interface RendezVousRepositoryInterface
{

    public function save(RendezVous $rendezVous): string;

    public function getRendezVousById(string $id): RendezVous;
}