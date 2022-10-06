<?php

namespace App\GraphQL\Mutations;

use App\Models\UserPreferences;

final class UpdateUserPreferences
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): UserPreferences
    {
        $input = $args['input'];
        $preferences = UserPreferences
                ::where('user_id', $input['user']['connect'])->first();
        $preferences->default_publication_status 
                = $input['defaultPublicationStatus'];
        $preferences->save();
        return $preferences;
    }
}
