<?php

namespace App\GraphQL\Queries;
use App\Actions\AutocompletePerson;

final class PersonAutocomplete
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $autocomplete = new AutocompletePerson;
        return $autocomplete($args['q']);
    }
}
