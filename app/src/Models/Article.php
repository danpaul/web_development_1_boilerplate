<?php

namespace App\Models;

class Article
{
    public ?int $id = null;
    public string $title;
    public string $content;
    public string $author;
    public ?string $created_at = null;
}
