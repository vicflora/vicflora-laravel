<?php

namespace App\GraphQL\Mutations;

use App\Actions\UpdateGroupPerson as ActionsUpdateGroupPerson;

class UpdateGroupPerson
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $updateGroupPerson = new ActionsUpdateGroupPerson;
        return $updateGroupPerson($input);
    }
}
