<?php

namespace Infrastructure\Repositories\Persistence\Block;

use \PDOException;
use Infrastructure\Database;
use PDO;

class BlockRepository
{
    public function __construct(private Database $database) {}

    public function getAll(string $table): array
    {
        $pdo = $this->database->getConnection();

        $sql = "SELECT * FROM $table";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBlockById(int $id): array|bool
    {
        $pdo = $this->database->getConnection();

        $sql = "SELECT * FROM blocks WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBlockByName(string $name): array|bool
    {
        $pdo = $this->database->getConnection();

        $sql = "SELECT * FROM blocks WHERE name = :name";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function addBlock(array $data): int
    {
        $pdo = $this->database->getConnection();
        $sql = "INSERT INTO blocks (name, description, elementos_del_bloque) VALUES (:name, :description, :elementos_del_bloque)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindValue(':elementos_del_bloque', $data['elementos_del_bloque'], PDO::PARAM_INT);
        $stmt->execute();
        return $pdo->lastInsertId(); // Devuelve el ID del nuevo bloque insertado 
    }

    public function updateBlock(int $id, array $data): bool
    {
        $sql = "UPDATE blocks SET name = :name, description = :description WHERE id = :id";

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new \Exception('Fallo al actualizar el bloque: ' . $e->getMessage());
        }
    }

    public function deleteBlock(int $id): bool
    {
        $pdo = $this->database->getConnection();

        // Obtener el bloque por defecto
        $defaultBlock = $this->getBlockByName('Bloque Principal');
        if (!$defaultBlock) {
            throw new \Exception('No se encontró el bloque por defecto "Bloque Principal".');
        }
        $defaultBlockId = $defaultBlock['id'];

        // Actualizar cualquier referencia del bloque a eliminar al bloque por defecto
        $sql = "UPDATE indicadores SET block_id = :defaultBlockId WHERE block_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':defaultBlockId', $defaultBlockId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Eliminar el bloque
        $sql = "DELETE FROM blocks WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new \Exception('Error al eliminar el bloque: ' . $e->getMessage());
        }
    }

    public function showIndicators(int $blockId): array
    {
        $pdo = $this->database->getConnection();
        $sql = "SELECT nombre FROM integrardb.indicadores WHERE block_id = :block_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':block_id', $blockId, PDO::PARAM_INT);
        $stmt->execute();

        // Devuelve todos los resultados asociados al block_id
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }


    public function showElements(int $id): array
    {
        $pdo = $this->database->getConnection();

        $sql = "SELECT elementos_del_bloque FROM blocks WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function updateElements(int $id, int $elementos_del_bloque): bool
    {
        $pdo = $this->database->getConnection();

        $sql = "UPDATE blocks SET elementos_del_bloque = :elementos_del_bloque WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':elementos_del_bloque', $elementos_del_bloque, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
