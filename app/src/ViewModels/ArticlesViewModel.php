<?php

namespace App\ViewModels;

class ArticlesViewModel
{
    /**
     * @param \App\Models\Article[] $articles
     */
    public function __construct(
        public string $title,
        /** @var \App\Models\Article[] */
        public array $articles,
    ) {
    }
}
