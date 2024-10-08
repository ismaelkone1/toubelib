<?php

namespace toubelib\application\middlewares;

class AddHearders {
 public function __invoke( 
    ServerRequestInterface $request, 
    RequestHandlerInterface $next): ResponseInterface {
    
        if (! $rq->hasHeader('Origin'))
        New HttpUnauthorizedException ($rq, "missing Origin Header (cors)");

    $response = $next->handle($request);

    $response = $response
        ->withHeader('Access-Control-Allow-Origin',  $rq->getHeader('Origin'))
        ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELETE, HEAD, PATCH' ) #TODO
        ->withHeader('Access-Control-Allow-Headers','Authorization' )
        ->withHeader('Access-Control-Max-Age', 3600)
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Content-Language', 'fr-FR')
        ->withHeader('Cache-Control', 'max-age='. 60*60*2);
    
    return $response;
    }}
