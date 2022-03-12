<?php

namespace App\GraphQL\Queries;

use App\Actions\CreateCitationMarkdown;
use App\Models\Reference;

class ReferenceCitationMarkdown
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Reference $reference, array $args)
    {
        $createCitationMarkdown = new CreateCitationMarkdown;
        return $createCitationMarkdown($reference);
    }
}
