<?php

namespace toubeelib\application\renderer;

use Psr\Http\Message\ResponseInterface as Response;

class JsonRenderer
{
    public static function render(Response $rs, int $code, mixed $data = null): Response
    {
        $rs = $rs->withStatus($code)
                 ->withHeader('Content-Type', 'application/json;charset=utf-8');

        if (!is_null($data)) {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($json === false) {
                throw new \RuntimeException('Erreur lors de l\'encodage des données en JSON : ' . json_last_error_msg());
            }

            $rs->getBody()->write($json);
        }

        return $rs;
    }
}
