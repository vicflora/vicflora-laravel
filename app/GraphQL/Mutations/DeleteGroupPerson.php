<?php

namespace App\GraphQL\Mutations;

use App\Actions\UpdateGroupPerson;
use App\Models\GroupPerson;

class DeleteGroupPerson
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $groupPerson = GroupPerson::where('guid', $args['id']);
        $groupPerson->delete();
        $updateGroupPerson = new UpdateGroupPerson;
        $higherSequenceMembers = GroupPerson::where('group_id', $groupPerson->group->id)
            ->where('sequence', '>', $groupPerson->sequence)
            ->get();
        if ($higherSequenceMembers->count()) {
            foreach ($higherSequenceMembers as $member) {
                $updateGroupPerson([
                    'id' => $member->id, 
                    'version' => $member->version - 1,
                ]);
            }
        }
        return $groupPerson;
    }
}
