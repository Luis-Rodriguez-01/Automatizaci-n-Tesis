<?php

namespace Infrastructure\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Infrastructure\Repositories\Persistence\Time\TimeRepository;
use Infrastructure\Database;

class TimeController
{
    private TimeRepository $repository;
    private Database $database;

    public function __construct(TimeRepository $repository, Database $database)
    {
        $this->repository = $repository;
        $this->database = $database;
    }

    // Método para establecer el rango de tiempo
    public function setTimeRange(Request $request, Response $response, array $args): Response
{
    $data = json_decode($request->getBody()->getContents(), true);
    $indicatorId = (int) $args['id'];

    // Verificar si el indicador con el ID dado existe
    if (!$this->repository->indicatorExists($indicatorId)) {
        $response->getBody()->write(json_encode([
            'error' => 'El ID del indicador no existe en la base de datos.'
        ]));
        return $response->withStatus(404);
    }

    // Validar que el rango de tiempo esté en los valores permitidos
    if (!isset($data['rango_de_tiempo']) || !in_array((int)$data['rango_de_tiempo'], [7, 30, 180])) {
        $response->getBody()->write(json_encode([
            'error' => 'El rango de tiempo debe ser 7, 30 o 180 días.'
        ]));
        return $response->withStatus(400);
    }

    // Convertir a entero y actualizar el rango de tiempo en la base de datos
    $rangoDeTiempo = (int) $data['rango_de_tiempo'];
    $this->repository->setTimeRange($indicatorId, $rangoDeTiempo);

    $response->getBody()->write(json_encode([
        'message' => 'Rango de tiempo actualizado exitosamente'
    ]));
    return $response->withStatus(200);
}

    // Método para obtener el rango de tiempo actual
    public function getTimeRange(Request $request, Response $response, array $args): Response
    {
    // Obtener el ID del indicador de los argumentos de la ruta
    $indicatorId = (int) $args['id'];

    // Llamar al repositorio para obtener el rango de tiempo del indicador específico
    $rango_de_tiempo = $this->repository->getTimeRange($indicatorId);

    $response->getBody()->write(json_encode([
        'rango_de_tiempo' => $rango_de_tiempo
    ]));
    return $response->withStatus(200);
    }
}