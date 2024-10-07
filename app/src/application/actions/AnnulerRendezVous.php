<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInvalidDataException;
use function FastRoute\cachedDispatcher;

class AnnulerRendezVous
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
        $id = $args['ID-RDV'] ?? null;

        try {
            $rdv = $this->serviceRendezVousInterface->annulerRendezvous($id);

            $data = [
                'rdv' => $rdv,
                'links' => [
                    'self' => '/rdv/' . $id
                ]
            ];

            return JsonRenderer::render($rs, 200, $data);
        } catch (ServiceRendezVousInvalidDataException $e) {
            return $rs->withStatus(400);
        }
        catch (\Exception $e) {
            return $rs->withStatus(500);
        }
    }
}