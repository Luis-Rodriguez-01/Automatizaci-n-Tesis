<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Persistence\Subscription;

use Infrastructure\Database;
use InvalidArgumentException;
use PDO;
class SubscriptionFactory
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(string $type): SubscriptionInterface
    {
        switch ($type) {
            case 'Basica':
                return new SubscriptionBasic(database: $this->database);

            case 'Media':
                return new SubscriptionMedium(database: $this->database);

            case 'Avanzada':
                return new SubscriptionAdvanced(database: $this->database);

            default:
                throw new InvalidArgumentException(message: "Tipo de suscripción no reconocido.");
        }
    }


    public function createSuscriptionb(string $tipo): array
{
    $pdo = $this->database->getConnection();

    // Definir el límite o criterios específicos según el tipo de suscripción
    $limite = match ($tipo) {
        'Basica' => 5,      // Ejemplo: obtener 5 indicadores para suscripción básica
        'Media' => 8,      // Ejemplo: obtener 8 indicadores para suscripción media
        'Avanzada' => null,   // Ejemplo: obtener todos los indicadores para suscripción avanzada
        default => 0,
    };

    // Consulta SQL para obtener los indicadores con el límite definido
    $sql = "SELECT nombre FROM indicadores LIMIT :limite";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

public function saveSubscription(array $data): ?string
{
    $pdo = $this->database->getConnection();
    $sql = "INSERT INTO suscripciones (suscripcion, indicadores) VALUES (:suscripcion, :indicadores)";
    $stmt = $pdo->prepare($sql);

    // Verificar y limpiar 'suscripcion'
    $suscripcion = trim($data['suscripcion']);

    // Log temporal para verificar el valor exacto de 'suscripcion'
    error_log("Valor de 'suscripcion': " . json_encode($suscripcion));

    if (!in_array($suscripcion, ['basica', 'media', 'avanzada'])) {
        throw new \InvalidArgumentException("Tipo de suscripción inválido: $suscripcion");
    }

    $stmt->bindParam(':suscripcion', $suscripcion);
    
    // Convertir 'indicadores' a JSON y enlazar
    $indicadoresJson = json_encode($data['indicadores']);
    $stmt->bindParam(':indicadores', $indicadoresJson);

    $stmt->execute();
    return $pdo->lastInsertId();
}

    
}