<?php

namespace App\Services;
use App\Models\Article;

interface IArticleService
{
    public function getAll(): array;
    public function create(Article $article): void;
    public function get(int $id): ?Article;
    public function update(Article $article): void;
    public function delete(int $id): void;
}