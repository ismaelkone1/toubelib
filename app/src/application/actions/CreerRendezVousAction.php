<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\auth\auth\rdv\ServiceRendezVousInvalidDataException;
use Respect\Validation\Validator as v;
use DateTimeImmutable;
use Exception;

class CreerRendezVousAction extends AbstractAction
{
    private ServiceRendezVousInterface $serviceRendezVous;

    /**
     * Constructeur de la classe.
     * @param ServiceRendezVousInterface $serviceRendezVous
     */
    public function __construct(ServiceRendezVousInterface $serviceRendezVous)
    {
        $this->serviceRendezVous = $serviceRendezVous;
    }

    /**
     * Méthode principale pour gérer la requête.
     */
    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        // Récupération des données du body de la requête
        $data = $rq->getParsedBody();

        // Validation des données d'entrée
        $validator = v::key('idPatient', v::stringType()->notEmpty())
                      ->key('creneau', v::stringType()->notEmpty())  // On validera la date après
                      ->key('praticien', v::stringType()->notEmpty())
                      ->key('specialitee', v::stringType()->notEmpty())
                      ->key('type', v::stringType()->notEmpty())
                      ->key('statut', v::stringType()->notEmpty());

        try {
            $validator->assert($data);
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }

        // Validation et conversion du créneau (en DateTimeImmutable)
        try {
            $creneau = new DateTimeImmutable($data['creneau']);
        } catch (Exception $e) {
            throw new HttpBadRequestException($rq, 'Invalid date format for creneau');
        }

        // Création du DTO pour le rendez-vous
        $inputRendezVousDTO = new InputRendezVousDTO(
            $data['idPatient'],
            $creneau,
            $data['praticien'],
            $data['specialitee'],
            $data['type'],
            $data['statut']
        );

        try {
            // Appel au service pour créer le rendez-vous
            $rdv = $this->serviceRendezVous->creerRendezVous($inputRendezVousDTO);

            // Création de la réponse avec les données du rendez-vous créé
            $responseData = [
                'rdv' => $rdv,
                'links' => [
                    'self' => [
                        "href" => '/rdv/' . $rdv->ID
                    ],
                    'modifier' => [
                        "href" => '/rdv/' . $rdv->ID
                    ],
                    'annuler' => [
                        "href" => '/rdv/' . $rdv->ID
                    ],
                    'praticien' => [
                        "href" => '/praticien/' . $rdv->getPraticien()
                    ],
                    'patient' => [
                        "href" => '/patient/' . $rdv->getIdPatient()
                    ]
                ]
            ];

            return JsonRenderer::render($rs, 201, $responseData);

        } catch (ServiceRendezVousInvalidDataException $e) {
            throw new HttpBadRequestException($rq, $e->getMessage());
        }
    }
}
