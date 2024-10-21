<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\PlanningPraticienDTO;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use Respect\Validation\Validator as v;
use toubeelib\core\services\rdv\ServiceRendezVousInvalidDataException;

class ConsulterPlanningPraticienAction extends AbstractAction
{

    private ServiceRendezVousInterface $serviceRendezVousInterface;

    public function __construct(ServiceRendezVousInterface $serviceRendezVousInterface)
    {
        $this->serviceRendezVousInterface = $serviceRendezVousInterface;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {

        $queryParams = $rq->getQueryParams();

        $debut = new \DateTimeImmutable($queryParams['debut']);
        $fin = new \DateTimeImmutable($queryParams['fin']);

        $planningPraticienDTO = new PlanningPraticienDTO($args['ID-PRATICIEN'], $debut, $fin, $queryParams['specialitee'], $queryParams['type']);

        $planningValidator = v::attribute('idPraticien', v::stringType()->notEmpty())
            ->attribute('start', v::instance('DateTimeImmutable'))
            ->attribute('end', v::instance('DateTimeImmutable'))
            ->attribute('specialitee', v::stringType()->notEmpty())
            ->attribute('type', v::stringType()->notEmpty());

        $planningPraticienDTO->setBusinessValidator($planningValidator);

        try {
            $planningPraticienDTO->validate();
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($args['ID-PRATICIEN'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $args['ID-PRATICIEN'] || (filter_var($queryParams['debut'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $queryParams['debut'] || (filter_var($queryParams['fin'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $queryParams['fin'] || (filter_var($queryParams['specialitee'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $queryParams['specialitee'] || (filter_var($queryParams['type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $queryParams['type']) {
            throw new HttpBadRequestException($rq, "Bad data format");
        }

        try {
            $planning = $this->serviceRendezVousInterface->listerPlanningPraticien($planningPraticienDTO);
        } catch (ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        $data = [
            'planning' => $planning,
            'links' => [
                'self' => [
                    "href" => '/planning/' . $args['ID-PRATICIEN']
                ]
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);

    }
}