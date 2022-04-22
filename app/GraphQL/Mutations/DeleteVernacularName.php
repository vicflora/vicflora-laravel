<?php

namespace App\GraphQL\Mutations;

use App\Models\VernacularName;

class DeleteVernacularName
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $vernacularName = VernacularName::where('guid', $args['id'])->first();
        $vernacularName->delete();
        return $vernacularName;
    }
}
