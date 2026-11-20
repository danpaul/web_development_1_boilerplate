<?php

namespace App\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use App\Services\IArticleService;
use App\ViewModels\ArticlesViewModel;
use App\ViewModels\ArticleViewModel;

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

    public function show($vars = [])
    {
        $article = $this->articleService->get((int) $vars['id']);
        if ($article === null) {
            http_response_code(404);
            echo 'Not Found';
            return;
        }
        $model = new ArticleViewModel($article->title, $article);
        require __DIR__ . '/../Views/articles/show.php';
    }

    public function store($vars = [])
    {
        $article = new Article();
        $article->title = $_POST['title'] ?? '';
        $article->content = $_POST['content'] ?? '';
        $article->author = $_POST['author'] ?? '';
        $this->articleService->create($article);
        header('Location: /articles', true, 302);
    }

    public function update($vars = [])
    {
        $article = $this->articleService->get((int) $vars['id']);
        if ($article === null) {
            http_response_code(404);
            echo 'Not Found';
            return;
        }
        $article->title = $_POST['title'] ?? '';
        $article->content = $_POST['content'] ?? '';
        $article->author = $_POST['author'] ?? '';
        $this->articleService->update($article);
        header('Location: /articles', true, 302);
    }

    public function destroy($vars = [])
    {
        $id = (int) $vars['id'];
        if ($this->articleService->get($id) === null) {
            http_response_code(404);
            echo 'Not Found';
            return;
        }
        $this->articleService->delete($id);
        header('Location: /articles', true, 302);
    }
}
