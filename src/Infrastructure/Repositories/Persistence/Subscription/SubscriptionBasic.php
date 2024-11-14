<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Persistence\Subscription;

use Infrastructure\Database;
use PDO;

class SubscriptionBasic implements SubscriptionInterface
{
    private Database $database;

    // Agregar Database en el constructor
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function createSubscription(string $tipo): array
    {
        $pdo = $this->database->getConnection();
        $sql = "SELECT nombre FROM indicadores LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}