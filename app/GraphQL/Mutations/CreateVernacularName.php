<?php

namespace App\GraphQL\Mutations;

use App\Models\TaxonConcept;
use App\Models\VernacularName;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateVernacularName
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];

        $vernacularName = new VernacularName;
        $vernacularName->guid = Str::uuid();
        $vernacularName->taxon_concept_id = TaxonConcept::where('guid', 
                $input['taxonConcept']['connect'])->value('id');
        $vernacularName->name = $input['name'];
        if (isset($input['is_preferred'])) {
            if ($input['is_preferred']) {
                DB::table('vernacular_names')
                        ->where('taxon_concept_id', 
                                $vernacularName->taxon_concept_id)
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
        $vernacularName->created_by_id = Auth::id();
        $vernacularName->version = 1;
        $vernacularName->save();
        return $vernacularName;
    }
}
