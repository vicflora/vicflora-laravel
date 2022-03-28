<?php

namespace App\GraphQL\Mutations;

use App\Models\Contributor;
use App\Models\Reference;

class DeleteContributors
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $reference = Reference::where('guid', $args['reference'])->first();
        Contributor::where('reference_id', $reference->id)->delete();
        return $reference;
    }
}
