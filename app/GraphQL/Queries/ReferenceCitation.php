<?php

namespace App\GraphQL\Queries;

use App\Actions\CreateCitation;
use App\Models\Reference;

class ReferenceCitation
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Reference $reference, array $args)
    {
        $createCitation = new CreateCitation;
        return $createCitation($reference);
    }
}
