<?php

namespace App\GraphQL\Mutations;

use App\Models\MultiAccessKeyCharacter;

final class DeleteMultiAccessKeyCharacter
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyCharacter
    {
        $character = MultiAccessKeyCharacter::where('guid', $args['id'])->first();

        $character->delete();

        return $character;
    }
}
