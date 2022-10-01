<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKey;
use App\Models\MultiAccessKeyFeature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class CreateMultiAccessKeyFeature
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyFeature
    {
        $input = $args['input'];

        $feature = new MultiAccessKeyFeature();
        $feature->guid = Str::uuid();
        $feature->version = 1;
        $feature->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        $feature->key_id = 
                MultiAccessKey::where('guid', $input['key']['connect'])
                ->value('id');
        if (isset($input['parent'])) {
            $feature->parent_id = MultiAccessKeyFeature
                    ::where('guid', $input['parent']['connect'])->value('id');
        }
        $feature->name = $input['name'];
        $feature->type = $input['type'];
        if (isset($input['description'])) {
            $feature->description = $input['description'];
        }
        $feature->save();
        return $feature;
    }
}
