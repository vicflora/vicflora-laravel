<?php

namespace App\GraphQL\Mutations;

use App\Models\Profile;

class DeleteProfile
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        list($guid, $version) = explode(':', $args['id']);
        $profile = Profile::where('guid', $guid)
                ->where('version', $version)
                ->first();
        $profile->delete();

        return $profile;
    }
}
