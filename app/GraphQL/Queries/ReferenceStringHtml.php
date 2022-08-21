<?php

namespace App\GraphQL\Queries;

use App\Actions\CreateReferenceStringHtml;
use App\Models\Reference;

class ReferenceStringHtml
{
    /**
     * @param  Reference  $reference
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Reference $reference, array $args)
    {
        $createReferenceStringHtml = new CreateReferenceStringHtml;
        return $createReferenceStringHtml($reference);
    }
}
