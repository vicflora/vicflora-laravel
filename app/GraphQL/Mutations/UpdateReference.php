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
        $id = Auth::id();
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
        if (isset($input['parent']['connect'])) {
            $input['parent_id'] = 
                    Reference::where('guid', $input['parent']['connect'])
                    ->value('id');
        }
        $reference = Reference::where('guid', $input['guid'])->first();
        $reference->update($input);
        $reference->increment('version');
        return $reference;
    }
}
