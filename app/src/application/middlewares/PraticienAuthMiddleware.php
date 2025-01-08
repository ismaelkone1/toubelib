<?php

namespace toubeelib\application\middlewares;

use toubeelib\core\services\auth\AuthzService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Response as SlimResponse;

class PraticienAuthMiddleware
{
    private AuthzService $authzService;

    public function __construct(AuthzService $authzService)
    {
        $this->authzService = $authzService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $auth = $request->getAttribute('auth');
        $praticienId = $request->getAttribute('route')->getArgument('id');

        if (!$this->authzService->canAccessPraticienProfile($auth, $praticienId)) {
            throw new HttpBadRequestException($request);
        }

        return $handler->handle($request);
    }
}
