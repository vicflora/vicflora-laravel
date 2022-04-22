<?php

namespace App\GraphQL\Mutations;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class UpdateProfile
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        list($guid, $version) = explode(':', $input['id']);
        $profile = Profile::where('guid', $guid)
            ->where('version', $version)
            ->first();
        $profile->profile = $input['profile'];
        $profile->modified_by_id = Auth::id();
        $profile->save();
        return $profile;
    }
}
