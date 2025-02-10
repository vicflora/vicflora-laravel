<?php
// Copyright 2025 Royal Botanic Gardens Board
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace App\Actions;

use Illuminate\Support\Facades\DB;

class INaturalistMigratePhotos {

    public function __invoke()
    {
        $photos = DB::table('inaturalist_prev.observation_photos as op')
            ->join('licenses as l', 'op.license_code', '=', 'l.name')
            ->select(
                'op.photo_id as id',
                'op.license_code',
                'op.original_dimensions',
                'op.attribution',
                'l.id as license_id'
            )
            ->get();

        $insertData = $photos->map(fn ($photo) => (array) $photo);
        $chunks = collect($insertData)->chunk(1000);
        foreach ($chunks as $chunk) {
            DB::table('inaturalist.photos')->insertOrIgnore($chunk->toArray());
        }

        return 0;
    }
}
