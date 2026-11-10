<?php

namespace App\Controllers;

use App\Services\ArticleService;
use App\Services\IArticleService;
use App\ViewModels\ArticlesViewModel;

class ArticleController
{
    private IArticleService $articleService;

    public function __construct()
    {
        $this->articleService = new ArticleService();
    }

    public function index($vars = [])
    {
        $articles = $this->articleService->getAll();
        $model = new ArticlesViewModel('Articles', $articles);
        require __DIR__ . '/../Views/articles/index.php';
    }
}
