<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInterface;
use Respect\Validation\Validator as v;

class ListerDispoPraticienAction extends AbstractAction
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

        $creneauValidator = v::key('debut', v::stringType()->notEmpty())
            ->key('fin', v::stringType()->notEmpty());

        $praticienIdValidator = v::stringType()->notEmpty();

        try {
            $creneauValidator->assert($queryParams);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        try {
            $praticienIdValidator->assert($args['ID-PRATICIEN']);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($args['ID-PRATICIEN'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $args['ID-PRATICIEN'] || (filter_var($queryParams['debut'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $queryParams['debut'] || (filter_var($queryParams['fin'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $queryParams['fin']) {
            throw new HttpBadRequestException($rq, "Bad data format");
        }

        $debut = new \DateTimeImmutable($queryParams['debut']);
        $fin = new \DateTimeImmutable($queryParams['fin']);

        $inputDispoPraticienDTO = new \toubeelib\core\dto\InputDispoPraticienDTO($args['ID-PRATICIEN'], $debut, $fin);

        try {
            $dispos = $this->serviceRendezVousInterface->listerDispoPraticien($inputDispoPraticienDTO);
        } catch (\toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        return JsonRenderer::render($rs, 200, $dispos);

    }
}