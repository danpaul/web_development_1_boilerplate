<?php

namespace App\Services;

interface IArticleService
{
    public function getAllArticles(): array;
    public function getArticleById(int $id): ?array;
    public function createArticle(array $data): int;
    public function updateArticle(int $id, array $data): bool;
    public function deleteArticle(int $id): bool;
}
