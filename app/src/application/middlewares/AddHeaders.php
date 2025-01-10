<?php

namespace toubeelib\application\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpUnauthorizedException;

class AddHeaders
{
    public function __invoke(
        ServerRequestInterface  $rq,
        RequestHandlerInterface $next): ResponseInterface
    {

        /**
         * if (! $rq->hasHeader('Origin'))
         * throw new HttpUnauthorizedException($rq, "missing Origin Header (CORS)");
         */
        $origin = $rq->hasHeader('Origin') ? $rq->getHeaderLine('Origin') : '*';
        $response = $next->handle($rq);

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELETE, HEAD, PATCH') #TODO
            ->withHeader('Access-Control-Allow-Headers', 'Authorization')
            ->withHeader('Access-Control-Max-Age', 3600)
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Content-Language', 'fr-FR')
            ->withHeader('Cache-Control', 'max-age=' . 60 * 60 * 2);

        return $response;
    }
}
