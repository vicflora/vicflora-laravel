<?php

namespace App\GraphQL\Queries;

use App\Actions\AutocompleteJournal;

class JournalAutocomplete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $autocomplete = new AutocompleteJournal;
        return $autocomplete($args['q']);
    }
}
