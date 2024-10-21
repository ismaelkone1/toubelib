<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\GererCycleRendezVousDTO;
use toubeelib\core\dto\ModificationRendezVousDTO;
use toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInvalidDataException;
use Respect\Validation\Validator as v;

class ModifierOuGererCycleRendezVousAction extends AbstractAction
{
    private ServiceRendezVousInterface $serviceRendezVousInterface;

    public function __construct(ServiceRendezVousInterface $serviceRendezVousInterface)
    {
        $this->serviceRendezVousInterface = $serviceRendezVousInterface;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $data = $rq->getParsedBody();

        if (isset($data['statut'])) {
            return $this->handleCycleRdv($rq, $rs, $args, $data);
        }

        return $this->handleModificationRdv($rq, $rs, $args, $data);
    }

    private function handleCycleRdv(ServerRequestInterface $rq, ResponseInterface $rs, array $args, array $data): ResponseInterface
    {
        $gererCyclerdvDto = new GererCycleRendezVousDTO($args['ID-RDV'], $data['statut']);
        $this->validateDto($gererCyclerdvDto, $rq);

        $this->validateInput($args['ID-RDV'], $data['statut'], $rq);

        try {
            $rdv = $this->serviceRendezVousInterface->gererCycleRdv($gererCyclerdvDto);
            return $this->renderResponse($rs, $rdv, $args['ID-RDV']);
        } catch (ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }

    private function handleModificationRdv(ServerRequestInterface $rq, ResponseInterface $rs, array $args, array $data): ResponseInterface
    {
        $this->validateId($args['ID-RDV'], $rq);
        $this->validateModificationInput($data, $rq);

        $this->validateInput($args['ID-RDV'], $data['specialitee'], $rq, $data['patient']);

        $modifierRdvDto = new ModificationRendezVousDTO($args['ID-RDV'], $data['patient'], $data['specialitee']);

        try {
            $rdv = $this->serviceRendezVousInterface->modifierRendezvous($modifierRdvDto);
            return $this->renderResponse($rs, $rdv, $args['ID-RDV']);
        } catch (ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }

    private function validateDto($dto, ServerRequestInterface $rq): void
    {
        $validator = v::attribute('id', v::stringType()->notEmpty())
            ->attribute('statut', v::stringType()->notEmpty());

        $dto->setBusinessValidator($validator);

        try {
            $dto->validate();
        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }

    private function validateId(string $id, ServerRequestInterface $rq): void
    {
        $idValidator = v::stringType()->notEmpty();

        try {
            $idValidator->assert($id);
        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }

    private function validateModificationInput(array $data, ServerRequestInterface $rq): void
    {
        $modifierRdvInputValidator = v::key('specialitee', v::optional(v::stringType()->notEmpty()))
            ->key('patient', v::optional(v::stringType()->notEmpty()));

        try {
            $modifierRdvInputValidator->assert($data);
        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }

    private function validateInput(string $id, string $specialitee, ServerRequestInterface $rq, string $patient = null): void
    {
        if ((filter_var($id, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $id ||
            (filter_var($specialitee, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $specialitee ||
            ($patient !== null && (filter_var($patient, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $patient)) {
            throw new HttpBadRequestException($rq, "Bad data format");
        }
    }

    private function renderResponse(ResponseInterface $rs, $rdv, string $idRdv): ResponseInterface
    {
        $data = [
            'rdv' => $rdv,
            'links' => [
                'self' => ["href" => '/rdv/' . $idRdv],
                'modifier' => ["href" => '/rdv/' . $idRdv],
                'annuler' => ["href" => '/rdv/' . $idRdv],
                'praticien' => ["href" => '/praticien/' . $rdv->getPraticien()],
                'patient' => ["href" => '/patient/' . $rdv->getIdPatient()]
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}