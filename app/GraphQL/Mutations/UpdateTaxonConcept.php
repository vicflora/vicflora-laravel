<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\Change;
use App\Models\DegreeOfEstablishment;
use App\Models\EstablishmentMeans;
use App\Models\OccurrenceStatus;
use App\Models\Reference;
use App\Models\TaxonConcept;
use App\Models\TaxonName;
use App\Models\TaxonomicStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UpdateTaxonConcept
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];

        $input['modified_by_id'] = Agent::where('user_id', Auth::id())->value('id');
        $input['taxon_name_id'] = 
                TaxonName::where('guid', $input['taxonName']['connect'])
                ->value('id');
        if (isset($input['accordingTo']) && $input['accordingTo']) {
            $input['according_to_id'] =
                    Reference::where('guid', $input['accordingTo']['connect'])
                    ->value('id');
        }
        if (isset($input['parent']) && $input['parent']) {
            $input['parent_id'] =
                    TaxonConcept::where('guid', $input['parent']['connect'])
                    ->value('id');
        }
        if (isset($input['acceptedConcept'])) {
            $input['accepted_id'] =
                    TaxonConcept::where('guid', $input['acceptedConcept']['connect'])
                    ->value('id');
        }

        if (isset($input['taxonomicStatus'])) {
            $input['taxonomic_status_id'] = 
                    TaxonomicStatus::where('name', 
                    $input['taxonomicStatus'])->value('id');
        }
        if (isset($input['occurrenceStatus'])) {
            $input['occurrence_status_id'] = 
                    OccurrenceStatus::where('name', 
                    $input['occurrenceStatus'])->value('id');
        }
        if (isset($input['establishmentMeans'])) {
            $input['establishment_means_id'] = 
                    EstablishmentMeans::where('name', 
                    $input['establishmentMeans'])->value('id');
        }
        if (isset($input['degreeOfEstablishment'])) {
            $input['degree_of_establishment_id'] = 
                    DegreeOfEstablishment::where('name', 
                    $input['degreeOfEstablishment'])->value('id');
        }

        $taxonConcept = TaxonConcept::where('guid', $input['guid'])->first();
        if ($input['taxonomic_status_id'] != $taxonConcept->taxonomic_status_id 
                || (isset($input['accepted_id']) && $input['accepted_id'] != $taxonConcept->accepted_id)
                || (!isset($input['accepted_id']) && $taxonConcept->accepted_id)) {
            $change = new Change();
            $change->guid = Str::uuid();
            $change->from_id = $taxonConcept->id;
            if (isset($input['taxonomicStatus']) && $input['taxonomicStatus'] == 'accepted') {
                $change->to_id = $taxonConcept->id;
            }
            else {
                $change->to_id = isset($input['accepted_id']) ? $input['accepted_id'] : null;
            }
            $change->change_type_id = $input['taxonomic_status_id'];
            $change->created_by_id = Agent::where('user_id', Auth::id())->value('id');
            $change->save();
        }
        $taxonConcept->publication_status = $input['publicationStatus'];

        $taxonConcept->update($input);
        $taxonConcept->increment('version');
        return $taxonConcept;
    }
}
