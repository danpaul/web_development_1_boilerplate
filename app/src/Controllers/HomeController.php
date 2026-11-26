<?php

namespace App\Controllers;

use App\Services\ContentfulService;
use App\Services\IContentfulService;
use App\ViewModels\HomeViewModel;

class HomeController
{
    private IContentfulService $contentfulService;

    public function __construct()
    {
        $this->contentfulService = new ContentfulService();
    }

    public function home($vars = [])
    {
        // Normally you would get the entry ID from the route parameter, database, or query string, but for this example, we'll use a hardcoded ID.

        // YOU SHOULD REPLACE THIS WITH YOUR OWN CONTENTFUL ENTRY ID
        $content = $this->contentfulService->getEntry('6C4zuCkd7Lok85PIFYmb5n');
        $vm = new HomeViewModel($content);
        require __DIR__ . '/../Views/home/index.php';
    }
}