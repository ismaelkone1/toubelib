<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\GererCycleRendezVousDTO;
use toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInterface;

class GererCycleRendezVousAction extends AbstractAction
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
        $queryParams = $rq->getQueryParams();

        $gererCyclerdvDto = new GererCycleRendezVousDTO($args['ID-RDV'], $queryParams['statut']);

        $gererCyclerdvValidator = v::attribute('id', v::stringType()->notEmpty())
            ->attribute('statut', v::stringType()->notEmpty());

        $gererCyclerdvDto->setBusinessValidator($gererCyclerdvValidator);

        try {
            $gererCyclerdvDto->validate();
        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($args['ID-RDV'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $args['ID-RDV'] || (filter_var($queryParams['statut'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $queryParams['statut']) {
            throw new HttpBadRequestException($rq, "Bad data format");
        }

        try {
            $rdv = $this->serviceRendezVousInterface->gererCycleRdv($gererCyclerdvDto);

            $data = [
                'rdv' => $rdv,
                'links' => [
                    'self' => [
                        "href" => '/rdv/' . $args['ID-RDV']
                    ],
                    'modifier' => [
                        "href" => '/rdv/' . $args['ID-RDV']
                    ],
                    'annuler' => [
                        "href" => '/rdv/' . $args['ID-RDV']
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
        } catch (\toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }
}