<?php

namespace App\Controllers;

use App\Services\ArticleService;
use App\Services\IArticleService;

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
        var_dump($articles);
    }
}