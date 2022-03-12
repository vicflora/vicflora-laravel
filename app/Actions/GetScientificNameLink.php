<?php
// Copyright 2022 Royal Botanic Gardens Board
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

class GetScientificNameLink 
{
    public function __invoke($name)
    {
        return DB::table('taxon_names as tn')
                ->join('taxon_concepts as tc', 'tn.id', '=', 'tc.taxon_name_id')
                ->where('tn.full_name', $name)
                ->orWhereRaw("substring(tn.full_name for 1)||'. '||substring(tn.full_name from position(' ' in tn.full_name) + 1)='$name'")
                ->orderBy('tc.according_to_id')
                ->value('tc.guid');
    }
}