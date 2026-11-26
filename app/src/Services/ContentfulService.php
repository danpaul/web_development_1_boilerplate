<?php

namespace App\Services;

use Contentful\Core\Exception\NotFoundException;
use Contentful\Delivery\Client;
use Contentful\Delivery\Resource\Entry;

class ContentfulService implements IContentfulService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(
            $_ENV['CONTENTFUL_ACCESS_TOKEN'],
            $_ENV['CONTENTFUL_SPACE_ID'],
            $_ENV['CONTENTFUL_ENVIRONMENT_ID'] ?? 'master'
        );
    }

    public function getEntry(string $id): ?Entry
    {
        try {
            return $this->client->getEntry($id);
        } catch (NotFoundException) {
            return null;
        }
    }

}