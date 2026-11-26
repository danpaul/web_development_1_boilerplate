<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\IArticleRepository;

class ArticleService implements IArticleService
{
    private IArticleRepository $articleRepository;
    public function __construct()
    {
        $this->articleRepository = new ArticleRepository();
    }

    public function getAll(): array
    {
        return $this->articleRepository->getAll();
    }

    public function create(Article $article): void
    {
        $this->articleRepository->create($article);
    }

    public function get(int $id): ?Article
    {
        return $this->articleRepository->get($id);
    }

    public function update(Article $article): void
    {
        $this->articleRepository->update($article);
    }

    public function delete(int $id): void
    {
        $this->articleRepository->delete($id);
    }

}