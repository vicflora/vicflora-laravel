<?php

namespace App\GraphQL\Mutations;

use App\Models\Reference;

class DeleteReference
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $reference = Reference::where('guid', $args['id'])->first();
        $reference->delete();
        return $reference;
    }
}
