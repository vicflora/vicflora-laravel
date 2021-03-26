<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonName;

class NameTypeAhead
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return TaxonName::where('full_name', 'ilike', "%{$args['q']}%")->pluck('full_name');
    }
}
