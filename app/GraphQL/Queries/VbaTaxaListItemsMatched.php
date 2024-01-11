<?php

namespace App\GraphQL\Queries;

use App\Models\VbaTaxaListItem;
use Illuminate\Database\Eloquent\Builder;

final class VbaTaxaListItemsMatched
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): Builder
    {
        return VbaTaxaListItem::whereNotNull('taxon_name_id');
    }
}
