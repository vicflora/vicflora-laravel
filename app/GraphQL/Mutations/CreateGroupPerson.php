<?php

namespace App\GraphQL\Mutations;

use App\Actions\CreateGroupPerson as ActionsCreateGroupPerson;

class CreateGroupPerson
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $createGroupPerson = new ActionsCreateGroupPerson;
        return $createGroupPerson($input);
    }
}
