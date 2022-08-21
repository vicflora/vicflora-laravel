<?php

namespace App\GraphQL\Queries;

use App\Actions\CreateReferenceStringMarkdown;
use App\Models\Reference;

class ReferenceStringMarkdown
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Reference $reference, array $args)
    {
        $createReferenceStringMarkdown = new CreateReferenceStringMarkdown;
        return $createReferenceStringMarkdown($reference);
    }
}
