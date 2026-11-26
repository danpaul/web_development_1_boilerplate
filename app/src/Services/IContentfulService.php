<?php

namespace App\Services;

use Contentful\Delivery\Resource\Entry;

interface IContentfulService
{
    public function getEntry(string $id): ?Entry;
}