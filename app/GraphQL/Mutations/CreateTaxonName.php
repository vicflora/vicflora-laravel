<?php

namespace App\GraphQL\Mutations;

use App\Models\NameType;
use App\Models\Reference;
use App\Models\TaxonName;
use App\Models\TaxonTreeDefItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateTaxonName
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        // print_r($input);
        $input['guid'] = Str::uuid();
        $input['version'] = 1;
        $input['created_by_id'] = Auth::id();

        if (isset($input['rank'])) {
            $input['name_rank_id'] = TaxonTreeDefItem::where('name', $input['rank'])->value('id');
        }
        if ($input['parent']) {
            $input['parent_name_id'] = TaxonName::where('guid', $input['parent']['connect'])->value('id');
        }
        if (isset($input['protologue']['connect'])) {
            $input['protologue_id'] = Reference::where('guid', $input['protologue']['connect'])->value('id');
        }
        if (isset($input['nameType'])) {
            $input['name_type_id'] = NameType::where('name', $input['nameTypeName'])->value('id');
        }
        $taxonName = TaxonName::create($input);
        return $taxonName;
    }
}
