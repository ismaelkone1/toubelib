<?php

namespace Gateway\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExampleAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write("Ceci est une action exemple.");
        return $response;
    }
}