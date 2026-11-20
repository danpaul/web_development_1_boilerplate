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
        $stmt = $this->pdo->prepare(
            'INSERT INTO articles (title, content, author) VALUES (:title, :content, :author)'
        );
        $stmt->bindValue(':title', $article->title);
        $stmt->bindValue(':content', $article->content);
        $stmt->bindValue(':author', $article->author);
        $stmt->execute();
    }

    public function get(int $id): ?Article
    {
        $stmt = $this->pdo->prepare('SELECT id, title, content, author, created_at FROM articles WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, Article::class);
        $article = $stmt->fetch();
        return $article ?: null;
    }

    public function update(Article $article): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE articles SET title = :title, content = :content, author = :author WHERE id = :id'
        );
        $stmt->bindValue(':title', $article->title);
        $stmt->bindValue(':content', $article->content);
        $stmt->bindValue(':author', $article->author);
        $stmt->bindValue(':id', $article->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

}