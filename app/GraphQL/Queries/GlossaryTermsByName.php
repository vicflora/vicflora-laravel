<?php

namespace App\GraphQL\Queries;

use App\Models\GlossaryTerm;

class GlossaryTermsByName
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return GlossaryTerm::where('name', 'ilike', $args['name'] . '%')
            ->orderBy('name')
            ->get();
    }
}
