<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\ReferenceType;
use Illuminate\Support\Facades\Auth;

class UpdateReference
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $id = Agent::where('user_id', Auth::id())->value('id');
        $input = $args['input'];
        $input['modified_by_id'] = $id;
        if (isset($input['referenceType'])) {
            $input['reference_type_id'] = 
                    ReferenceType::where('name', $input['referenceType'])
                    ->value('id');
        }
        if (isset($input['author']['connect'])) {
            $input['author_id'] = 
                    Agent::where('guid', $input['author']['connect'])
                    ->value('id');
        }
        if (isset($input['journal']['connect'])) {
            $input['parent_id'] = 
                    Reference::where('guid', $input['journal']['connect'])
                    ->value('id');
        }
        if (isset($input['book']['connect'])) {
            $input['parent_id'] = 
                    Reference::where('guid', $input['book']['connect'])
                    ->value('id');
        }
        $reference = Reference::where('guid', $input['guid'])->first();
        $reference->update($input);
        $reference->increment('version');
        return $reference;
    }
}
