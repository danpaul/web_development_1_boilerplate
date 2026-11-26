<?php

namespace App\ViewModels;

use Contentful\Delivery\Resource\Entry;

class HomeViewModel
{
    public ?Entry $content;

    public function __construct(
        ?Entry $content,
    ) {
        $this->content = $content;
    }
}