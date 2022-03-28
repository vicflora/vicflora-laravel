<?php

namespace App\GraphQL\Mutations;

use App\Models\DegreeOfEstablishment;
use App\Models\EstablishmentMeans;
use App\Models\OccurrenceStatus;
use App\Models\Reference;
use App\Models\TaxonConcept;
use App\Models\TaxonName;
use App\Models\TaxonomicStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateTaxonConcept
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @return TaxonConcept
     */
    public function __invoke($_, array $args): TaxonConcept
    {
        $input = $args['input'];
        $input['guid'] = Str::uuid();
        $input['version'] = 1;
        $input['created_by_id'] = Auth::id();

        $input['taxon_name_id'] = 
                TaxonName::where('guid', $input['taxonName']['connect'])
                ->value('id');
        if ($input['accordingTo']) {
            $input['according_to_id'] =
                    Reference::where('guid', $input['accordingTo']['connect'])
                    ->value('id');
        }
        if ($input['parent']) {
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
        $taxonConcept = TaxonConcept::create($input);
        return $taxonConcept;
    }
}
