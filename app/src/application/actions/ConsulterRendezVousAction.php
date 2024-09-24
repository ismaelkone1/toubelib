<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRendezVous;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInvalidDataException;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;

class ConsulterRendezVousAction
{

    private ServiceRendezVousInterface $serviceRendezVousInterface;

    /**
     * @param ServiceRendezVousInterface $serviceRendezVousInterface
     */
    public function __construct(ServiceRendezVousInterface $serviceRendezVousInterface)
    {
        $this->serviceRendezVousInterface = $serviceRendezVousInterface;
    }


    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        //On essaye de récupérer l'id donné en paramètre
        $id = $args['ID-RDV'] ?? null;

        try {
            $rdv = $this->serviceRendezVousInterface->getRendezVousById($id);

            $data = [
                'rdv' => $rdv
            ];
            return JsonRenderer::render($rs, 200, $data);
        } catch (ServiceRendezVousInvalidDataException $e) {
            return JsonRenderer::render($rs, 400, ['error' => $e->getMessage()]);
        }

    }
}