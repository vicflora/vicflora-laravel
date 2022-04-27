<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\Reference;
use App\Models\TaxonConcept;
use App\Models\TaxonConceptReference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateTaxonConceptReference
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $taxonConceptReference = new TaxonConceptReference();
        $taxonConceptReference->guid = Str::uuid();
        $taxonConceptReference->taxon_concept_id = 
                TaxonConcept::where('guid', $input['taxonConcept'])
                ->value('id');
        $taxonConceptReference->reference_id = 
                Reference::where('guid', $input['reference'])->value('id');
        $taxonConceptReference->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        $taxonConceptReference->save();
        return $taxonConceptReference;
    }
}
