<?php

declare(strict_types=1);

namespace Infrastructure;

use PDO;

class Database
{

    private string $host;
    private string $name;
    private string $user;
    private string $password;
    private \PDO $pdo;

    public function __construct(string $host, string $name, string $user, string $password)
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;

        // Inicializar la conexión PDO aquí
        $dsn = "mysql:host=$this->host;dbname=$this->name;charset=utf8";
        $this->pdo = new \PDO($dsn, $this->user, $this->password);

    }

    public function getConnection(): PDO
    {
        $dsn = "mysql:host=$this->host; dbname=$this->name;charset=utf8";

        $pdo = new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        return $pdo;
    }
}
