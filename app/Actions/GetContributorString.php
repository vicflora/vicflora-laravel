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

use App\Models\Contributor;
use App\Models\Reference;

class GetContributorString
{
    public function __invoke(Reference $reference)
    {
        $contributors = Contributor::join('references',
        'contributors.reference_id', '=', 'references.id')
        ->join('contributor_roles', 'contributors.contributor_role_id',
                '=', 'contributor_roles.id')
        ->join('agents', 'contributors.agent_id', '=', 'agents.id')
        ->where('references.id', $reference->id)
        ->select('references.id as reference_id',
                'contributors.sequence',
                'agents.last_name',
                'agents.initials',
                'contributor_roles.name as role')
        ->get();

        $agents = [];
        foreach ($contributors as $index => $contributor) {
          if ($index === 0) {
              $role = $contributor->role;
              $agents[] = $contributor->last_name . ', ' . $contributor->initials;
          }
          else {
              $agents[] = $contributor->initials . ' ' . $contributor->last_name;
          }
        }

        $count = count($agents);
        if ($count) {
          if ($count > 1) {
              $str = implode(', ', array_slice($agents, 0, $count-1))
                      . ' & ' . $agents[$count-1];
          }
          else {
              $str = $agents[0];
          }
          if ($role !== 'Author') {
              if ($count > 1) {
                  $str .= ' (eds)';
              }
              else {
                  $str .= ' (ed.)';
              }
          }
          return $str;
        }
        return null;
    }
}