<?php

namespace App\Controllers;

use Contentful\Delivery\Client;

class ContentfulTestController
{
    public function hello($vars = [])
    {
        $client = new Client(
            getenv('CONTENTFUL_ACCESS_TOKEN'),
            getenv('CONTENTFUL_SPACE_ID'),
            getenv('CONTENTFUL_ENVIRONMENT_ID') ?: 'master'
        );

        $space = $client->getSpace();
        $entries = $client->getEntries();

        // normally we don't want to echo from a controller method directly
        // but rather load a view template
        echo "Hello, Contentful! Connected to space \"{$space->getName()}\" with {$entries->getTotal()} entries.";

        echo '<pre>';
        var_dump($entries[0]->getDescription() ?? null);
        echo '</pre>';
    }
}