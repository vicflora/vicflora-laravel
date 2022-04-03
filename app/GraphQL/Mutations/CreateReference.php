<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\ReferenceType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateReference
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $input['guid'] = Str::uuid();
        $input['version'] = 1;
        $input['created_by_id'] = Auth::id();
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

        $reference = Reference::create($input);
        return $reference;
    }
}
