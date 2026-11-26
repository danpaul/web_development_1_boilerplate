<?php

namespace App\ViewModels;

use App\Models\Article;

class ArticleViewModel
{
    public function __construct(
        public string $title,
        public Article $article,
    ) {
    }
}
