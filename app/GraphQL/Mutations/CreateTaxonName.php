<?php

namespace App\GraphQL\Mutations;

use App\Models\NameType;
use App\Models\Reference;
use App\Models\TaxonName;
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
        $input['guid'] = Str::uuid();
        $input['version'] = 1;
        $input['created_by_id'] = Auth::id();

        if (isset($input['protologue']['connect'])) {
            $input['protologue_id'] = Reference::where('guid', $input['protologue']['connect'])->value('id');
        }
        if (isset($input['nameTypeName'])) {
            $input['name_type_id'] = NameType::where('name', $input['nameTypeName'])->value('id');
        }
        $taxonName = TaxonName::create($input);
        return $taxonName;
    }
}
