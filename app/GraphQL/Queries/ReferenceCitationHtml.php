<?php

namespace App\GraphQL\Queries;

use App\Actions\CreateCitationHtml;
use App\Models\Reference;

class ReferenceCitationHtml
{
    /**
     * @param  Reference  $reference
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Reference $reference, array $args)
    {
        $createCitationHtml = new CreateCitationHtml;
        return $createCitationHtml($reference);
    }
}
