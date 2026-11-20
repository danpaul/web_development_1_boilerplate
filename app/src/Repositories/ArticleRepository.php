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
        
    }

    public function get(int $id): ?Article
    {
        return $this->getById($id);
    }

    public function update(Article $article): void
    {
        
    }

    public function delete(int $id): void
    {
    }

}