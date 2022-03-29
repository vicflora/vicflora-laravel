<?php

namespace App\GraphQL\Queries;

use App\Models\GlossaryTerm;
use Illuminate\Support\Facades\DB;

class GlossaryTermsInString
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args): ?array
    {
        $ret = null;
        preg_match_all('/\b[\w-]{3,}\b/', $args['string'], $matches);
        if ($matches[0]) {
            $uniqueWords = array_unique($matches[0]);
            $values = [];
            foreach ($uniqueWords as $value) {
                $values[] = "'$value'";
            }
            $values = implode(',', $values);

            $sql = <<<SQL
SELECT item, t.id 
from terms t
join unnest(array[$values]) as item
    on t.name=lower(item) or t.name||'s'=lower(item)
where t.glossary_id = 4
SQL;

            $result = DB::connection('glossary')->select($sql);

            $ids = collect($result)->map(function ($item) {
                return $item->id;
            });

            $terms = GlossaryTerm::whereIn('id', $ids)->get();

            foreach ($result as $row) {
                $ret[] = [
                    'substring' => $row->item,
                    'term' => $terms->first(function ($term, $key) use ($row) {
                        return $term->id == $row->id;
                    }),
                ];
            }
        }
        return $ret ?: null;
    }
}
