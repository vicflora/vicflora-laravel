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
use App\Models\Agent;
use Illuminate\Database\Eloquent\Collection;

class AutocompletePerson {

    public function __invoke(String $q): Collection
    {
        return Agent::select('agents.*')
            ->join('agent_types as at2', 'agents.agent_type_id', '=', 'at2.id')
            ->where('at2.name', 'Person')
            ->where('agents.name', 'ilike', "$q%")
            ->orderBy('agents.name')
            ->get();
    }
}
