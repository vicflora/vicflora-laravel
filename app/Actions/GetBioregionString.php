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

use App\Models\TaxonBioregion;
use App\Models\TaxonConcept;

class GetBioregionString 
{
    public function __invoke(TaxonConcept $taxonConcept) 
    {
        $regions = TaxonBioregion::where('taxon_concept_id',
                        $taxonConcept->guid)
                ->select('bioregion_name', 'bioregion_code',
                        'occurrence_status', 'establishment_means')
                ->orderBy('bioregion_id')
                ->get();
        $ret = [];
        foreach ($regions as $region) {
            $str = '';
            if ($region->establishment_means !== 'native') {
                $str .= '*';
            }
            $str = '<span class="region" title="' . $region->bioregion_name
                    . '">' . $region->bioregion_code . '</span>';
            $ret[] = $str;
        }
        if ($ret) {
            return implode(', ', $ret) . '.';
        }
        return null;
    }
}