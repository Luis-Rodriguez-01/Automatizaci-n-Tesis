<?php

namespace Infrastructure\Repositories\Persistence\Time;

use Infrastructure\Database;

class TimeRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    // Método para actualizar el rango de tiempo en la base de datos
    public function setTimeRange(int $indicatorId, int $days): void
{
    $pdo = $this->database->getConnection();
    $sql = "UPDATE indicadores SET rango_de_tiempo = :days WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':days', $days, \PDO::PARAM_INT);
    $stmt->bindParam(':id', $indicatorId, \PDO::PARAM_INT);
    $stmt->execute();
}

    // Método para obtener el rango de tiempo desde la base de datos
    public function getTimeRange(int $indicatorId): int
{
    $pdo = $this->database->getConnection();
    $sql = "SELECT rango_de_tiempo FROM indicadores WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $indicatorId, \PDO::PARAM_INT);
    $stmt->execute();

    // Devuelve el rango de tiempo o un valor predeterminado en caso de que no exista
    $rango_de_tiempo = $stmt->fetchColumn();
    return $rango_de_tiempo !== false ? (int) $rango_de_tiempo : 0;
}

    public function indicatorExists(int $indicatorId): bool
{
    $pdo = $this->database->getConnection();
    $sql = "SELECT COUNT(*) FROM indicadores WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $indicatorId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() > 0;
}
}