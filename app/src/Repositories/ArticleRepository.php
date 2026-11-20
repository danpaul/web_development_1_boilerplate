<?php

namespace App\Repositories;
use App\Models\Article;
use App\Framework\Repository;

class ArticleRepository extends Repository implements IArticleRepository
{

    public function __construct()
    {
        parent::__construct('articles', Article::class);
    }

    public function getAll(): array
    {
        $stmt = $this->getPDO()->query('SELECT * FROM articles');
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
        return $this->getById($id);
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