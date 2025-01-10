<?php

namespace toubeelib\application\middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key; 
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware
{
    private string $jwtSecret;

    public function __construct(string $jwtSecret)
    {
        $this->jwtSecret = $jwtSecret;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader('Authorization');
        
        if (!$authHeader || empty($authHeader[0])) {
            throw new HttpException($request, "header invalide", 401);
        }

        $token = str_replace('Bearer ', '', $authHeader[0]);

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

            $request = $request->withAttribute('auth', $decoded);

        } catch (\Exception $e) {
            throw new HttpException($request, "Invalid token", 401);
        }

        return $handler->handle($request);
    }
}
