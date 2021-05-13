<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Facades\DB;

class GlossaryTermFirstLetters
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return DB::connection('glossary')
                ->table('terms')
                ->where('glossary_id', 4)
                ->groupBy(DB::raw('substring(upper(name) from 1 for 1)'))
                ->orderBy(DB::raw('substring(upper(name) from 1 for 1)'))
                ->pluck(DB::raw('substring(upper(name) from 1 for 1) as first_letter'));
    }
}
