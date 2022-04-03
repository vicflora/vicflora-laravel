<?php

namespace App\GraphQL\Mutations;

use App\Models\TaxonName;
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
        $id = Auth::id();
        $taxonName = TaxonName::where('guid', $args['input']['guid'])->first();
        $input = $args['input'];
        $input['modified_by_id'] = $id;
        $taxonName->update($input);
        $taxonName->increment('version');
        return $taxonName;
    }
}
