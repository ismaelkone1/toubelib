<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRendezVous;
use toubeelib\core\services\rdv\ServiceRendezVousInvalidDataException;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;

class ConsulterRendezVousAction
{

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args)
    {
        //On essaye de récupérer l'id donné en paramètre
        $id = $args['ID-RDV'] ?? null;

        $service = new ServiceRendezVous(new ArrayRdvRepository());

        try {
            $rdv = $service->getRendezVousById($id);
            return JsonRenderer::render($rs, 200, $rdv);
        } catch (ServiceRendezVousInvalidDataException $e) {
            return JsonRenderer::render($rs, 400, ['error' => $e->getMessage()]);
        }

    }
}