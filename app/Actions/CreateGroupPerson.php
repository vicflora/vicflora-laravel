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
use App\Models\GroupPerson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateGroupPerson {
    
    public function __invoke(Array $input): GroupPerson
    {
        $groupPerson = new GroupPerson();
        $groupPerson->guid = Str::uuid();
        $groupPerson->sequence = $input['sequence'];
        $groupPerson->group_id = Agent::where('guid', $input['group']['connect'])->value('id');
        $groupPerson->member_id = Agent::where('guid', $input['member']['connect'])->value('id');
        $groupPerson->created_by_id = Agent::where('user_id', Auth::id())->value('id');
        $groupPerson->version = 1;
        $groupPerson->save();
        return $groupPerson;
    }
}