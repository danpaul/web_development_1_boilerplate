<?php

namespace App\Framework;

class Repository
{
    protected static \PDO $pdo;
    protected string $tableName;
    protected string $modelClass;

    public function __construct($tableName, $modelClass)
    {
        $this->tableName = $tableName;
        $this->modelClass = $modelClass;
        $connection_string = "mysql:host=$_ENV[DB_HOST];dbname=$_ENV[DB_NAME];charset=utf8mb4";
        self::$pdo = new \PDO($connection_string, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }

    public static function getPDO(): \PDO
    {
        return self::$pdo;
    }

    public function getAll(): array
    {
        $stmt = self::$pdo->query("SELECT * FROM {$this->tableName}");
        $results = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->modelClass);
        return $results ?: [];
    }

    public function getById(int $id): ?object
    {
        $stmt = self::$pdo->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->modelClass);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}