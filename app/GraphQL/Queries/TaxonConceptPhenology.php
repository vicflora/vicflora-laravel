<?php

namespace App\GraphQL\Queries;

use App\Actions\GetPhenology;
use App\Actions\GetPhenologyHigherTaxon;
use App\Models\TaxonConcept;
use Illuminate\Support\Facades\DB;

final class TaxonConceptPhenology
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke(?TaxonConcept $taxonConcept, array $args)
    {
        if (!$taxonConcept && isset($args['taxonConceptId'])) {
            $taxonConcept = TaxonConcept::where('guid', 
                    $args['taxonConceptId'])->first();
        }

        $getPhenology = new GetPhenology;
        $getPhenologyHigherTaxon = new GetPhenologyHigherTaxon;

        if ($taxonConcept->rank_id >= 220) {
            return $getPhenology($taxonConcept->guid);
        }
        elseif ($taxonConcept->rank_id == 180) {
            $species = DB::table('taxon_concepts as tc')
                    ->join('taxon_concepts as tc1', 'tc.id', '=', 'tc1.parent_id')
                    ->where('tc.guid', $taxonConcept->guid)
                    ->pluck('tc1.guid');
            return $getPhenologyHigherTaxon($taxonConcept->guid, $species);
        }
        elseif ($taxonConcept->rank_id == 140) {
            $species = DB::table('taxon_concepts as tc')
                    ->join('taxon_concepts as tc1', 'tc.id', '=', 'tc1.parent_id')
                    ->join('taxon_concepts as tc2', 'tc1.id', '=', 'tc2.parent_id')
                    ->where('tc.guid', $taxonConcept->guid)
                    ->pluck('tc2.guid');
            return $getPhenologyHigherTaxon($taxonConcept->guid, $species);
        }
        else {
            return [];
        }
    }
}
