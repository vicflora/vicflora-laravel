<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonName;
use Illuminate\Support\Facades\DB;

final class TaxonNameApniName
{
    public function __invoke(TaxonName $taxonName, array $args)
    {

        $apniNumber = <<<SQL
case
    when coalesce(an.id, an2.id) is not null
        then substring(coalesce(an.id, an2.id) from length(coalesce(an.id, an2.id)) - position('/' in reverse(coalesce(an.id, an2.id))) + 2)
    else null
end as "apniNumber"
SQL;

        $matchType = <<<SQL
case
    when an.id is not null then 'fullNameWithAuthorship'
    when an2.id is not null then 'fullName'
    else null
end as "matchType"
SQL;

        return DB::table('taxon_names as tn')
                ->leftJoin('apni.apni_names as an', 'tn.full_name_with_authorship', '=', 'an.scientific_name_with_authorship')
                ->leftJoin('apni.apni_names as an2', 'tn.full_name', '=', 'an.scientific_name')
                ->where('tn.id', $taxonName->id)
                ->whereRaw('coalesce(an.id, an2.id) is not null')
                ->select(
                    DB::raw('coalesce(an.id, an2.id) as id'),
                    DB::raw($apniNumber),
                    DB::raw('coalesce(an.scientific_name_with_authorship, an2.scientific_name_with_authorship) as "fullNameWithAuthorship"'),
                    DB::raw($matchType)
                )
                ->orderByRaw('coalesce(an.nomenclatural_status, an2.nomenclatural_status)')
                ->first();
    }
}
