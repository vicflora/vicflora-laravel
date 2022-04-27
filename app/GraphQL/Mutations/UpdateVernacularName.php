<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\VernacularName;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateVernacularName
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];

        $vernacularName = VernacularName::where('guid', $input['id'])->first();
        $vernacularName->name = $input['name'];
        if (isset($input['is_preferred'])) {
            if ($input['is_preferred']) {
                DB::table('vernacular_names')
                        ->where('taxon_concept_id', $vernacularName->taxon_concept_id)
                        ->where('id', '!=', $vernacularName->id)
                        ->update(['is_preferred' => false]);
            }
            $vernacularName->is_preferred = $input['is_preferred'];
        }
        if (isset($input['name_usage'])) {
            $vernacularName->name_usage = $input['name_usage'];
        }
        if (isset($input['remarks'])) {
            $vernacularName->name_usage = $input['remarks'];
        }
        $vernacularName->modified_by_id = Agent::where('user_id', Auth::id())->value('id');
        $vernacularName->version++;
        $vernacularName->save();
        return $vernacularName;
    }
}
