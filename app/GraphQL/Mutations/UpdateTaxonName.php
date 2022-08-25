<?php

namespace App\GraphQL\Mutations;

use App\Models\Agent;
use App\Models\NameType;
use App\Models\Reference;
use App\Models\TaxonName;
use App\Models\TaxonTreeDefItem;
use Illuminate\Support\Facades\Auth;

class UpdateTaxonName
{
    /**
     * Undocumented function
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @return TaxonName|null
     */
    public function __invoke($_, array $args): ?TaxonName
    {   
        $id = Agent::where('user_id', Auth::id())->value('id');
        $taxonName = TaxonName::where('guid', $args['input']['guid'])->first();
        $input = $args['input'];
        if (isset($input['name_type'])) {
            $input['name_type_id'] = NameType::where('name', $input['nameType'])->value('id');
        }
        if (isset($input['rank'])) {
            $input['name_rank_id'] = TaxonTreeDefItem::where('name', $input['rank'])->value('id');
        }
        if (isset($input['parent']['connect'])) {
            $input['parent_name_id'] = TaxonName::where('guid', $input['parent']['connect'])->value('id');
        }
        if (isset($input['protologue']['connect'])) {
            $input['protologue_id'] = Reference::where('guid', $input['protologue']['connect'])->value('id');
        }
        $input['modified_by_id'] = $id;
        $taxonName->update($input);
        $taxonName->increment('version');
        return $taxonName;
    }
}
