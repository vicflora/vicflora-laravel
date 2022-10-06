<?php

namespace App\GraphQL\Mutations;

use App\Models\MultiAccessKeyCharacter;

final class DeleteMultiAccessKeyCharacterCascade
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyCharacter
    {
        $character = MultiAccessKeyCharacter::where('guid', $args['id'])->first();

        if ($character->states->count()) {
            foreach ($character->states as $state) {
                $state->delete();
            }
        }

        if ($character->children->count()) {
            foreach ($character->children as $child) {
                $child->delete();
            }
        }

        $character->delete();

        return $character;
    }
}
