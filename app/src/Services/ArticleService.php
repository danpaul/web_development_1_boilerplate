<?php

// NOTE: we are using a simplified filed based storage for demo purposes
// For your assignment, you should use a database

namespace App\Services;

use App\Utils\JsonStore;

class ArticleService implements IArticleService
{
    private JsonStore $store;
    private const DATA_FILE = __DIR__ . '/../data/articles.json';

    public function __construct()
    {
        $this->store = new JsonStore(self::DATA_FILE);
    }

    public function getAllArticles(): array
    {
        return $this->store->getAll();
    }

    public function getArticleById(int $id): ?array
    {
        return $this->store->getById($id);
    }

    public function createArticle(array $data): int
    {
        return $this->store->create($data);
    }

    public function updateArticle(int $id, array $data): bool
    {
        return $this->store->update($id, $data);
    }

    public function deleteArticle(int $id): bool
    {
        return $this->store->delete($id);
    }
}
