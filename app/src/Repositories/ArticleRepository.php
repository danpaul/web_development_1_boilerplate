<?php

namespace App\Repositories;
use App\Models\Article;

class ArticleRepository implements IArticleRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $connection_string = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME']
        );
        $this->pdo = new \PDO($connection_string, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM articles');
        $results = $stmt->fetchAll(\PDO::FETCH_CLASS, Article::class);
        return $results ?: [];
    }

    public function create(Article $article): void
    {
        
    }

    public function get(int $id): ?Article
    {
        return null;
    }

    public function update(Article $article): void
    {
        
    }

    public function delete(int $id): void
    {
    }

}