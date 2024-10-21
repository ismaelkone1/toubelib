<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\IdPraticienDTO;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\praticien\ServicePraticienInvalidDataException;

class ConsulterPraticienAction extends AbstractAction
{
    private ServicePraticienInterface $servicePraticienInterface;

    public function __construct(ServicePraticienInterface $servicePraticienInterface)
    {
        $this->servicePraticienInterface = $servicePraticienInterface;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args['ID-PRATICIEN'] ?? null;

        $idPraticienDTO = new IdPraticienDTO($id);

        $idValidator = Validator::attribute('id', Validator::stringType()->notEmpty());

        $idPraticienDTO->setBusinessValidator($idValidator);
        try {
            $idPraticienDTO->validate();
        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        if ((filter_var($id, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) !== $id) {
            throw new HttpBadRequestException($rq, "Bad data format");
        }

        try {
            $praticien = $this->servicePraticienInterface->getPraticienById($idPraticienDTO);

            $data = [
                'praticien' => $praticien,
                'links' => [
                    'self' => [
                        "href" => '/praticiens/' . $id
                    ],
                    'modifier' => [
                        "href" => '/praticiens/' . $id
                    ],
                    'supprimer' => [
                        "href" => '/praticiens/' . $id
                    ],
                    'specialites' => [
                        "href" => '/praticiesn/' . $id . '/specialites'
                    ]
                ]
            ];

            return JsonRenderer::render($rs, 200, $data);
        } catch (ServicePraticienInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }
}