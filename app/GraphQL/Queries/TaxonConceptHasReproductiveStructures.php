<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;
use Illuminate\Support\Facades\DB;

final class TaxonConceptHasReproductiveStructures
{
    /**
     * @param  TaxonConcept $taxonConcept
     * @param  array{}  $args
     */
    public function __invoke(TaxonConcept $taxonConcept, array $args)
    {
        if ($taxonConcept->rank_id >= 220 || $taxonConcept->rank_id < 140) {
            $ids = [$taxonConcept->guid];
        }
        elseif ($taxonConcept->rank_id == 180) {
            $ids = DB::table('taxon_concepts as tc')
                    ->join('taxon_concepts as tc1', 'tc.id', '=', 'tc1.parent_id')
                    ->where('tc.guid', $taxonConcept->guid)
                    ->pluck('tc1.guid');
        }
        elseif ($taxonConcept->guid) {
            $ids = DB::table('taxon_concepts as tc')
                    ->join('taxon_concepts as tc1', 'tc.id', '=', 'tc1.parent_id')
                    ->join('taxon_concepts as tc2', 'tc1.id', '=', 'tc2.parent_id')
                    ->where('tc.guid', $taxonConcept->guid)
                    ->pluck('tc2.guid');
        }

        $count = DB::table('mapper.taxon_concept_phenology_view')
                ->whereIn('taxon_concept_id', $ids)
                ->value(DB::raw('sum(buds)+sum(flowers)+sum(fruit) as num'));

        return $count > 0;
    }
}
