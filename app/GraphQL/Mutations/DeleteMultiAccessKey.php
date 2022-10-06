<?php

namespace App\GraphQL\Mutations;

use App\Models\MultiAccessKey;

final class DeleteMultiAccessKey
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKey
    {
        $key = MultiAccessKey::where('guid', $args['id'])->first();

        $key->delete();
        
        return $key;
    }
}
