<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;
use toubeelib\core\providers\AuthProvider;
use Slim\Psr7\Response as SlimResponse;

class SigninAction
{
    private AuthProvider $authProvider;

    public function __construct(AuthProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            throw new HttpException($request);
        }

        $authDTO = $this->authProvider->signin($email, $password);

        $responseBody = [
            'accessToken' => $authDTO->getAccessToken(),
            'refreshToken' => $authDTO->getRefreshToken()
        ];

        $response->getBody()->write(json_encode($responseBody));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
