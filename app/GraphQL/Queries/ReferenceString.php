<?php

namespace App\GraphQL\Queries;

use App\Actions\CreateReferenceString;
use App\Models\Reference;

class ReferenceString
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Reference $reference, array $args)
    {
        $createReferenceString = new CreateReferenceString;
        return $createReferenceString($reference);
    }
}
