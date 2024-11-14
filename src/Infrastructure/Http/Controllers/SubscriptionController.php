<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Infrastructure\Repositories\Persistence\Subscription\SubscriptionFactory;
use Infrastructure\Database;



class SubscriptionController
{   
    private SubscriptionFactory $factory;
    private Database $database;

    public function __construct(SubscriptionFactory $factory, Database $database)
    {
        $this->factory = $factory;
        $this->database = $database;
    }
    public function createSubscription(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['suscripcion']) || !in_array($data['suscripcion'], ['basica', 'media', 'avanzada'])) {
            $response->getBody()->write(json_encode(['error' => 'Tipo de suscripción no válido']));
            return $response->withStatus(400);
        }

        $type = $data['suscripcion'];  

        switch ($type) {
            case 'basica':
                return $this->createBasicSubscription($request, $response, $data);

            case 'media':
                return $this->createMediumSubscription($request, $response, $data);

            case 'avanzada':
                return $this->createAdvancedSubscription($request, $response, $data);

            default:
                $response->getBody()->write(json_encode(['error' => 'Tipo de suscripcion no válida']));
                return $response->withStatus(400);
        }
    }

    private function createBasicSubscription(Request $request, Response $response, array $data): Response
    {
        // Indicadores específicos para suscripción básica
        $data['indicadores'] = $this->getIndicatorsByLimit(limit: 4); 

        // Guardar suscripción en la base de datos
        $subscriptionId = $this->saveSubscription(data: $data);

        $response->getBody()->write(json_encode(value: [
            'message' => 'Suscripción básica creada',
            'subscription_id' => $subscriptionId
        ]));
        return $response;
    }

    private function createMediumSubscription(Request $request, Response $response, array $data): Response
    {
        // Indicadores específicos para suscripción avanzada
        $data['indicadores'] = $this->getIndicatorsByLimit(limit: 7); 

        // Guardar suscripción en la base de datos
        $subscriptionId = $this->saveSubscription($data);

        $response->getBody()->write(json_encode([
            'message' => 'Suscripción media creada',
            'subscription_id' => $subscriptionId
        ]));
        return $response;
    }

    private function createAdvancedSubscription(Request $request, Response $response, array $data): Response
    {
        // Indicadores específicos para suscripción avanzada
        $data['indicadores'] = $this->getIndicatorsByLimit(limit: null); 

        // Guardar suscripción en la base de datos
        $subscriptionId = $this->saveSubscription($data);

        $response->getBody()->write(json_encode([
            'message' => 'Suscripción avanzada creada',
            'subscription_id' => $subscriptionId
        ]));
        return $response;
    }

    // Método para guardar la suscripción en la base de datos
    private function saveSubscription(array $data): ?string
    {
        return $this->factory->saveSubscription($data);
    }

    // Método para obtener indicadores desde la base de datos
private function getIndicatorsByLimit(?int $limit): array
{
    $pdo = $this->database->getConnection();
    $sql = "SELECT nombre FROM indicadores";
    
    if ($limit !== null) {
        $sql .= " LIMIT :limite";
    }
    
    $stmt = $pdo->prepare($sql);

    if ($limit !== null) {
        $stmt->bindParam(':limite', $limit, \PDO::PARAM_INT);
    }

    $stmt->execute();

    // Recuperar solo los nombres de los indicadores
    $indicators = $stmt->fetchAll(\PDO::FETCH_COLUMN);
    return $indicators;
}
}