<?php

namespace App\Controllers;

use App\Services\ContentfulService;
use App\Services\IContentfulService;

class ContentfulTestController
{
    private IContentfulService $contentfulService;

    public function __construct()
    {
        $this->contentfulService = new ContentfulService();
    }

    public function hello($vars = [])
    {
        $space = $this->contentfulService->getSpace();
        $entries = $this->contentfulService->getEntries();

        // normally we don't want to echo from a controller method directly
        // but rather load a view template
        echo "Hello, Contentful! Connected to space \"{$space->getName()}\" with {$entries->getTotal()} entries.";

        echo '<pre>';
        var_dump($entries[0]->getDescription() ?? null);
        echo '</pre>';
    }
}
