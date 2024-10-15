<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\ModificationRendezVousDTO;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInvalidDataException;
use Respect\Validation\Validator as v;

class ModifierRendezVousAction extends AbstractAction
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

        $data = $rq->getParsedBody();

        $modifierRdvInputValidator = v::key('ID-RDV', v::stringType()->notEmpty())
            ->key('specialitee', v::optional(v::stringType()->notEmpty()))
            ->key('patient', v::optional(v::stringType()->notEmpty()));

        try {
            $modifierRdvInputValidator->assert($data);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($args['ID-RDV'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $args['ID-RDV'] || (filter_var($data['specialitee'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $data['specialitee'] || (filter_var($data['patient'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $data['patient']) {
            throw new HttpBadRequestException($rq, "Bad data format");
        }

        $modifierRdvDto = new ModificationRendezVousDTO($args['ID-RDV'], $data['patient'], $data['specialitee']);

        try {

            $rdv = $this->serviceRendezVousInterface->modifierRendezvous($modifierRdvDto);

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
        } catch (ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }
}