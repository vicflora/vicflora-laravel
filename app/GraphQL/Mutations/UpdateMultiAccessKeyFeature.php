<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\MultiAccessKeyFeature;
use Illuminate\Support\Facades\Auth;

final class UpdateMultiAccessKeyFeature
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): MultiAccessKeyFeature
    {
        $input = $args['input'];

        $feature = MultiAccessKeyFeature::where('guid', $input['id'])->first();
        $feature->modified_by_id = Agent::where('user_id', Auth::id())->value('id');
        if (isset($input['name'])) {
            $feature->name = $input['name'];
        }
        if (isset($input['featureType'])) {
            $feature->feature_type = $input['featureType'];
        }
        if (isset($input['description'])) {
            $feature->description = $input['description'];
        }
        if (isset($input['parent'])) {
            $feature->parent_id = MultiAccessKeyFeature
                    ::where('guid', $input['parent']['connect'])->value('id');
        }
        $feature->increment('version');
        $feature->save();
        return $feature;
    }
}
