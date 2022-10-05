<?php
// Copyright 2022 Royal Botanic Gardens Board
// 
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
// 
//     http://www.apache.org/licenses/LICENSE-2.0
// 
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace App\Actions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class BuildSolrIndexQuery {
    
    /**
     * Builds query for updating the SOLR Index. Returns a query builder to 
     * which a where condition and an output serialization (get(), first(),
     * toSql(), etc.) can be added.
     *
     * @return Builder
     */
    public function __invoke(): Builder
    {
        $children = DB::table('taxon_concepts')
                ->select('parent_id')
                ->whereNotNull('parent_id')
                ->groupBy('parent_id');

        $vernacularNames = DB::table('vernacular_names')
                ->select('taxon_concept_id', DB::raw("json_agg(name) as vernacular_names"))
                ->groupBy('taxon_concept_id');

        $bioregions = DB::table('mapper.taxon_bioregions')
                ->select('taxon_concept_id', DB::raw("json_agg(bioregion_name) as bioregions"))
                ->groupBy('taxon_concept_id');

        $lgas = DB::table('mapper.taxon_local_government_areas')
                ->select('taxon_concept_id', DB::raw("json_agg(local_government_area_name) as local_government_areas"))
                ->groupBy('taxon_concept_id');

        $parks = DB::table('mapper.taxon_park_reserves')
                ->select('taxon_concept_id', DB::raw("json_agg(park_reserve_name) as park_reserves"))
                ->groupBy('taxon_concept_id');

        $raps = DB::table('mapper.taxon_raps')
                ->select('taxon_concept_id', DB::raw("json_agg(rap_name) as registered_aboriginal_parties"))
                ->groupBy('taxon_concept_id');

        $contributors = DB::table('references as r')
                ->join('contributors as c', 'r.id', '=', 'c.reference_id')
                ->join('agents as a', 'c.agent_id', '=', 'a.id')
                ->groupBy('r.id')
                ->select('r.id as reference_id', DB::raw("string_agg(a.name, ' | ' order by c.\"sequence\") as contributors"));

        $first = DB::table('images')
                ->select('accepted_id as taxon_id', 'subtype')
                ->groupBy('accepted_id', 'subtype');

        $med = DB::table('specimen_images')
                ->select('accepted_id as taxon_id', DB::raw("'Specimen image' as subtype"))
                ->groupBy('accepted_id')
                ->union($first);

        $media = DB::table('taxon_concepts as tc')
                ->joinSub($med, 'med', 'tc.id', '=', 'med.taxon_id')
                ->select('tc.id as taxon_id', DB::raw("string_agg(med.subtype, ' | ' order by med.subtype) as media"))
                ->groupBy('tc.id');

        $query = DB::table('taxon_concepts as t')
                ->join('taxon_names as n', 't.taxon_name_id', '=', 'n.id')
                ->leftJoin('taxon_tree_def_items as r', 't.taxon_tree_def_item_id', '=', 'r.id')
                ->leftJoin('name_types as nt', 'n.name_type_id', '=', 'nt.id')
                ->leftJoin('nomenclatural_statuses as ns', 'n.nomenclatural_status_id', '=', 'ns.id')

                ->leftJoin('references as prot', 'n.published_in_id', '=', 'prot.id')
                ->leftJoin('references as acc', 't.according_to_id', '=', 'acc.id')
                ->leftJoinSub($contributors, 'acc_contr', 'acc.id', '=', 'acc_contr.reference_id')

                ->leftJoin('taxonomic_statuses as ts', 't.taxonomic_status_id', '=', 'ts.id')
                ->leftJoin('occurrence_statuses as os', 't.occurrence_status_id', '=', 'os.id')
                ->leftJoin('establishment_means as em', 't.establishment_means_id', '=', 'em.id')
                ->leftJoin('degree_of_establishment as de', 't.degree_of_establishment_id', '=', 'de.id')

                ->leftJoin('taxon_concepts as at', 't.accepted_id', '=', 'at.id')
                ->leftJoin('taxon_names as an', 'at.taxon_name_id', '=', 'an.id')
                ->leftJoin('taxon_tree_def_items as ar', 'at.taxon_tree_def_item_id', '=', 'ar.id')

                ->leftJoin('taxon_concepts as pt', 't.parent_id', '=', 'pt.id')
                ->leftJoin('taxon_names as pn', 'pt.taxon_name_id', '=', 'pn.id')

                ->leftJoin('taxon_concepts as t0', DB::raw('coalesce(at.id, t.id)'), '=', 't0.id')
                ->leftJoin('taxon_concepts as t1', 't0.parent_id', '=', 't1.id')
                ->leftJoin('taxon_concepts as t2', 't1.parent_id', '=', 't2.id')
                ->leftJoin('taxon_concepts as t3', 't2.parent_id', '=', 't3.id')
                ->leftJoin('taxon_concepts as t4', 't3.parent_id', '=', 't4.id')
                ->leftJoin('taxon_concepts as t5', 't4.parent_id', '=', 't5.id')
                ->leftJoin('taxon_concepts as t6', 't5.parent_id', '=', 't6.id')
                ->leftJoin('taxon_concepts as t7', 't6.parent_id', '=', 't7.id')
                ->leftJoin('taxon_concepts as t8', 't7.parent_id', '=', 't8.id')
                ->leftJoin('taxon_concepts as t9', 't8.parent_id', '=', 't9.id')
                ->leftJoin('taxon_concepts as t10', 't9.parent_id', '=', 't10.id')

                ->leftJoin('taxon_tree_def_items as td0', 't0.taxon_tree_def_item_id', '=', 'td0.id')
                ->leftJoin('taxon_tree_def_items as td1', 't1.taxon_tree_def_item_id', '=', 'td1.id')
                ->leftJoin('taxon_tree_def_items as td2', 't2.taxon_tree_def_item_id', '=', 'td2.id')
                ->leftJoin('taxon_tree_def_items as td3', 't3.taxon_tree_def_item_id', '=', 'td3.id')
                ->leftJoin('taxon_tree_def_items as td4', 't4.taxon_tree_def_item_id', '=', 'td4.id')
                ->leftJoin('taxon_tree_def_items as td5', 't5.taxon_tree_def_item_id', '=', 'td5.id')
                ->leftJoin('taxon_tree_def_items as td6', 't6.taxon_tree_def_item_id', '=', 'td6.id')
                ->leftJoin('taxon_tree_def_items as td7', 't7.taxon_tree_def_item_id', '=', 'td7.id')
                ->leftJoin('taxon_tree_def_items as td8', 't8.taxon_tree_def_item_id', '=', 'td8.id')
                ->leftJoin('taxon_tree_def_items as td9', 't9.taxon_tree_def_item_id', '=', 'td9.id')
                ->leftJoin('taxon_tree_def_items as td10', 't10.taxon_tree_def_item_id', '=', 'td0.id')

                ->leftJoin('taxon_names as tn0', 't0.taxon_name_id', '=', 'tn0.id')
                ->leftJoin('taxon_names as tn1', 't1.taxon_name_id', '=', 'tn1.id')
                ->leftJoin('taxon_names as tn2', 't2.taxon_name_id', '=', 'tn2.id')
                ->leftJoin('taxon_names as tn3', 't3.taxon_name_id', '=', 'tn3.id')
                ->leftJoin('taxon_names as tn4', 't4.taxon_name_id', '=', 'tn4.id')
                ->leftJoin('taxon_names as tn5', 't5.taxon_name_id', '=', 'tn5.id')
                ->leftJoin('taxon_names as tn6', 't6.taxon_name_id', '=', 'tn6.id')
                ->leftJoin('taxon_names as tn7', 't7.taxon_name_id', '=', 'tn7.id')
                ->leftJoin('taxon_names as tn8', 't8.taxon_name_id', '=', 'tn8.id')
                ->leftJoin('taxon_names as tn9', 't9.taxon_name_id', '=', 'tn9.id')
                ->leftJoin('taxon_names as tn10', 't10.taxon_name_id', '=', 'tn10.id')

                ->leftJoinSub($children, 'ct', function($join) {
                    $join->on('t.id', '=', 'ct.parent_id');
                })

                ->leftJoin('vernacular_names as pvn', function($join) {
                    $join->on('t.id', '=', 'pvn.taxon_concept_id')
                            ->where('pvn.is_preferred', '=', true);
                })
                ->leftJoinSub($vernacularNames, 'vn', function($join) {
                    $join->on('t.id', '=', 'vn.taxon_concept_id');
                })

                ->leftJoinSub($bioregions, 'bio', function($join) {
                    $join->on('t.guid', '=', 'bio.taxon_concept_id');
                })

                ->leftJoinSub($lgas, 'lga', function($join) {
                    $join->on('t.guid', '=', 'lga.taxon_concept_id');
                })

                ->leftJoinSub($parks, 'pr', function($join) {
                    $join->on('t.guid', '=', 'pr.taxon_concept_id');
                })

                ->leftJoinSub($raps, 'raps', function($join) {
                    $join->on('t.guid', '=', 'raps.taxon_concept_id');
                })

                ->leftJoin('vba.vba_taxa as vba', 'n.guid', '=', 'vba.taxon_name_id')

                ->leftJoin('descriptions as d', 't.id', '=', 'd.taxon_concept_id')

                ->leftJoinSub($media, 'media', 't.id', '=', 'media.taxon_id')

                ->select(
                        't.guid as id',
                        'n.guid as name_id',
                        'n.full_name',
                        'n.authorship',
                        'nt.name as name_type',
                        'ns.name as nomenclatural_status',
                        'prot.guid as published_in_id',
                        'prot.citation',
                        'prot.publication_year',
                        'acc.title as according_to',
                        'acc.guid as according_to_id',
                        'acc_contr.contributors as according_to_author',
                        'acc.publication_year as according_to_publication_year',
                        'r.name as rank',
                        'ts.name as taxonomic_status',
                        'os.name as occurrence_status',
                        'em.name as establishment_means',
                        'de.name as degree_of_establishment',
                        't.is_endemic',
                        't.has_introduced_occurrences',
                        'at.guid as accepted_name_id',
                        'an.full_name as accepted_name',
                        'an.authorship as accepted_name_authorship',
                        'ar.name as accepted_name_rank',
                        'pt.guid as parent_id',
                        'pn.full_name as parent_name',
                        'pn.authorship as parent_name_authorship',
                        'pvn.name as preferred_vernacular_name',
                        'vn.vernacular_names',
                        't.remarks',
                        DB::raw('lower(epbc_desc) as epbc'),
                        DB::raw('lower(ffg_desc) as ffg'),
                        DB::raw('lower(vic_adv_desc) as vic_advisory'),
                        'd.description',
                        'media.media'
                );

        $species = <<<SQL
case
    when td0.name='species' then tn0.full_name
    when td1.name='species' then tn1.full_name
    else null
end as species
SQL;
        $query->addSelect(DB::raw($species));

        $genus = <<<SQL
case
    when td0.name='genus' then tn0.full_name
    when td1.name='genus' then tn1.full_name
    when td2.name='genus' then tn2.full_name
    else null
end as genus
SQL;
        $query->addSelect(DB::raw($genus));

        $family = <<<SQL
case
    when td0.name='family' then tn0.full_name
	when td1.name='family' then tn1.full_name
	when td2.name='family' then tn2.full_name
	when td3.name='family' then tn3.full_name
	when td4.name='family' then tn4.full_name
	when td5.name='family' then tn5.full_name
	when td6.name='family' then tn6.full_name
	when td7.name='family' then tn7.full_name
	when td8.name='family' then tn8.full_name
	else null
end as family
SQL;
        $query->addSelect(DB::raw($family));

        $order = <<<SQL
case
    when td0.name='order' then tn0.full_name
	when td1.name='order' then tn1.full_name
	when td2.name='order' then tn2.full_name
	when td3.name='order' then tn3.full_name
	when td4.name='order' then tn4.full_name
	when td5.name='order' then tn5.full_name
	when td6.name='order' then tn6.full_name
	when td7.name='order' then tn7.full_name
	when td8.name='order' then tn8.full_name
	else null
end as "order"
SQL;
        $query->addSelect(DB::raw($order));

        $class = <<<SQL
case
    when td0.name='class' then tn0.full_name
	when td1.name='class' then tn1.full_name
	when td2.name='class' then tn2.full_name
	when td3.name='class' then tn3.full_name
	when td4.name='class' then tn4.full_name
	when td5.name='class' then tn5.full_name
	when td6.name='class' then tn6.full_name
	when td7.name='class' then tn7.full_name
	when td8.name='class' then tn8.full_name
	else null
end as "class"
SQL;
        $query->addSelect(DB::raw($class));

        $phylum = <<<SQL
case
    when td0.name='phylum' then tn0.full_name
	when td1.name='phylum' then tn1.full_name
	when td2.name='phylum' then tn2.full_name
	when td3.name='phylum' then tn3.full_name
	when td4.name='phylum' then tn4.full_name
	when td5.name='phylum' then tn5.full_name
	when td6.name='phylum' then tn6.full_name
	when td7.name='phylum' then tn7.full_name
	when td8.name='phylum' then tn8.full_name
	when td9.name='phylum' then tn9.full_name
	when td10.name='phylum' then tn10.full_name
	else null
end as "phylum"
SQL;
        $query->addSelect(DB::raw($phylum));

        $kingdom = <<<SQL
case
    when td0.name='kingdom' then tn0.full_name
	when td1.name='kingdom' then tn1.full_name
	when td2.name='kingdom' then tn2.full_name
	when td3.name='kingdom' then tn3.full_name
	when td4.name='kingdom' then tn4.full_name
	when td5.name='kingdom' then tn5.full_name
	when td6.name='kingdom' then tn6.full_name
	when td7.name='kingdom' then tn7.full_name
	when td8.name='kingdom' then tn8.full_name
	when td9.name='kingdom' then tn9.full_name
	when td10.name='kingdom' then tn10.full_name
	else null
end as "kingdom"
SQL;
        $query->addSelect(DB::raw($kingdom));

        $endOrHigher = <<<SQL
case
  	when ct.parent_id is not null then 'higher'
	else 'end'
end as end_or_higher_taxon
SQL;
        $query->addSelect(DB::raw($endOrHigher));

        $genericName = <<<SQL
case
    when n.full_name like '% %' then substring(n.full_name for position(' ' in n.full_name)-1)
	else null
end as generic_name
SQL;
        $query->addSelect(DB::raw($genericName));

        $specificEpithet = <<<SQL
case
  	when n.full_name like '% %' and pn.full_name not like '% %' then n.name_part
	when pn.full_name like '% %' then pn.name_part
	else null
end as specific_epithet
SQL;
        $query->addSelect(DB::raw($specificEpithet));

        $infraspecificEpithet = <<<SQL
case
  	when pn.full_name like '% %' then n.name_part
	else null
end as infraspecific_epithet
SQL;
        $query->addSelect(DB::raw($infraspecificEpithet));

        $query->addSelect('bio.bioregions',
            'lga.local_government_areas',
            'pr.park_reserves',
            'raps.registered_aboriginal_parties'
        );

        $query->addSelect('t.publicaton_status');

        return $query;
    }
}