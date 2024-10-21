<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\IdPatientDTO;
use toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInterface;
use Respect\Validation\Validator as v;

class ListerRendezVousPatientAction extends AbstractAction
{

    private ServiceRendezVousInterface $serviceRendezVousInterface;

    public function __construct(ServiceRendezVousInterface $serviceRendezVousInterface)
    {
        $this->serviceRendezVousInterface = $serviceRendezVousInterface;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $patientId = $args['ID-PATIENT'] ?? null;

        $idPatientDTO = new IdPatientDTO($patientId);

        $idpatientValidator = v::stringType()->notEmpty();

        try {
            $idpatientValidator->assert($patientId);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($patientId, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $patientId) {
            throw new HttpBadRequestException($rq, "Bad data format");
        }

        try {
            $rdvs = $this->serviceRendezVousInterface->getRendezVousPatient($idPatientDTO);
        } catch (\toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        return JsonRenderer::render($rs, 200, $rdvs);
    }
}