<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
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

        $idValidator = Validator::stringType()->notEmpty();

        try {
            $idValidator->assert($id);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            return JsonRenderer::render($rs, 400, ['error' => $e->getMessages()]);
        }

        if ((filter_var($id, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $id) {
            return JsonRenderer::render($rs, 400, ['error' => 'Bad data format']);
        }

        try {
            $rdv = $this->serviceRendezVousInterface->annulerRendezvous($id);

            $data = [
                'rdv' => $rdv,
                'links' => [
                    'self' => [
                        "href" => '/rdv/' . $id
                    ],
                    'modifier' => [
                        "href" => '/rdv/' . $id
                    ],
                    'annuler' => [
                        "href" => '/rdv/' . $id
                    ],
                    'praticien' => [
                        "href" => '/praticien/' . $rdv->getPraticien()
                    ],
                    'patient' => [
                        "href" => '/patient/' . $rdv->getIdPatient()
                    ]
                ]
            ];

            return JsonRenderer::render($rs, 200, $data);
        } catch (ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessages());
        }
    }
}