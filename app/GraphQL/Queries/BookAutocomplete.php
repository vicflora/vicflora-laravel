<?php

namespace App\GraphQL\Queries;

use App\Actions\AutocompleteReference;

class BookAutocomplete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $autocomplete = new AutocompleteReference;
        return $autocomplete($args['q'], 'Book');
    }
}
