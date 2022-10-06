<?php

namespace App\GraphQL\Mutations;

use App\Models\UserPreferences;

final class CreateUserPreferences
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): UserPreferences
    {
        $input = $args['input'];
        $preferences = new UserPreferences;
        $preferences->user_id = $input['user']['connect'];
        if (isset($input['defaultPublicationStatus'])) {
            $preferences->default_publication_status 
                    = $input['defaultPublicationStatus'];
        }
        $preferences->save();
        return $preferences;
    }
}
