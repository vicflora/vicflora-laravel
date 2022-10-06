<?php

namespace App\GraphQL\Mutations;

use App\Models\MultiAccessKeyState;

final class DeleteMultiAccessKeyState
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyState
    {
        $state = MultiAccessKeyState::where('guid', $args['id'])->first();
        $state->delete();
        return $state;
    }
}
