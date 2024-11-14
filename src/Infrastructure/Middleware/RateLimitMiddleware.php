<?php

declare(strict_types=1);

namespace Infrastructure\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;


//Puede ser mejorado con el uso de dependencias como: 
//"nikolaposa/rate-limit": "^3.2" y Redis para almacenar los datos
class RateLimitMiddleware
{
    private $requests = [];
    private $limit;
    private $interval;

    public function __construct(int $limit = 60, int $interval = 60)
    {
        $this->limit = $limit;
        $this->interval = $interval;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'];
        $now = time();

        // Limpiar solicitudes antiguas
        $this->requests = array_filter($this->requests, function($timestamp) use ($now) {
            return $timestamp > $now - $this->interval;
        });

        // Contar solicitudes para esta IP
        $requestsCount = count(array_filter($this->requests, function($timestamp, $requestIp) use ($ip) {
            return $requestIp === $ip;
        }, ARRAY_FILTER_USE_BOTH));

        if ($requestsCount >= $this->limit) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Rate limit exceeded']));
            return $response->withStatus(429)->withHeader('Content-Type', 'application/json');
        }

        // Registrar esta solicitud
        $this->requests[] = [$ip => $now];

        return $handler->handle($request);
    }
}