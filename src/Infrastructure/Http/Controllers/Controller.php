<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;
use Infrastructure\Repositories\Persistence\Block\BlockRepository;

class Controller
{
    public function __construct(private BlockRepository $repository, private Validator $validator)
    {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'description' => ['required'],
        ]);
    }

    private function validateRequestData(array $data, array $rules): bool
    {
        $this->validator->validate($data, $rules);

        // Evitar errores duplicados
        foreach ($this->validator->errors() as $field => $messages) {
            $this->validator->errors()[$field] = array_unique($messages);
        }

        // Verificar si hay errores
        return empty($this->validator->errors());
    }

    public function getAllBlocks(Request $request, Response $response): Response
    {
        $data = $this->repository->getAll("blocks");
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getBlockById(Request $request, Response $response, string $id): Response
    {
        $data = $this->repository->getBlockById((int) $id);

        if ($data === false) {
            $response->getBody()->write(json_encode(['mensaje' => 'Bloque no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createBlock(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        // Valida los datos
        if (! $this->validateRequestData($body, ['name' => 'required', 'description' => 'required'])) {
            $response->getBody()->write(json_encode($this->validator->errors()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        // Verificar si el nombre ya existe
        if ($this->repository->getBlockByName($body['name'])) {
            $response->getBody()->write(json_encode(['mensaje' => 'El nombre especificado ya existe']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        // Crear el bloque
        $id = $this->repository->addBlock($body);
        $response->getBody()->write(json_encode(['mensaje' => 'Bloque creado', 'id' => $id]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function updateExistingBlock(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();
        if (! $this->validateRequestData($body, ['name' => 'required', 'description' => 'required'])) {
            $response->getBody()->write(json_encode($this->validator->errors()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        if (!$this->repository->getBlockById((int) $id)) {
            $response->getBody()->write(json_encode(['mensaje' => 'No existe un bloque con ese id']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->repository->updateBlock((int) $id, $body);
        $response->getBody()->write(json_encode(['mensaje' => 'Bloque actualizado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteExistingBlock(Request $request, Response $response, string $id): Response
    {
        if (!$this->repository->getBlockById((int) $id)) {
            $response->getBody()->write(json_encode(['mensaje' => 'No existe un bloque con ese id']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->repository->deleteBlock((int) $id);
        $response->getBody()->write(json_encode(['mensaje' => 'Bloque eliminado correctamente']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public function showIndicatorsByBlocks(Request $request, Response $response, string $id): Response
    {
        // Verifica si el bloque existe
        if (!$this->repository->getBlockById((int) $id)) {
            $response->getBody()->write(json_encode(['mensaje' => 'No existe un bloque con ese id']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Obtiene los indicadores asociados al bloque
        $indicators = $this->repository->showIndicators((int) $id);
        if (empty($indicators)) {
            $response->getBody()->write(json_encode(['mensaje' => 'No se encontraron indicadores']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Devuelve los indicadores encontrados
        $response->getBody()->write(json_encode($indicators));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function showElementsByBlocks(Request $request, Response $response, string $id): Response
    {
        if (!$this->repository->getBlockById((int) $id)) {
            $response->getBody()->write(json_encode(['mensaje' => 'No existe un bloque con ese id']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $elements = $this->repository->showElements((int) $id);
        if (empty($elements)) {
            $response->getBody()->write(json_encode(['mensaje' => 'No se encontraron elementos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($elements));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateElementsByBlocks(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();
        $elementosDelBloque = $body['elementos_del_bloque'] ?? null;

        if (!is_int($elementosDelBloque)) {
            $response->getBody()->write(json_encode(['mensaje' => 'Datos inválidos para elementos del bloque']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        if (!$this->repository->getBlockById((int) $id)) {
            $response->getBody()->write(json_encode(['mensaje' => 'No existe un bloque con ese id']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->repository->updateElements((int) $id, $elementosDelBloque);
        $response->getBody()->write(json_encode(['mensaje' => 'Elementos actualizados correctamente']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
