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

use App\Models\Profile;
use App\Models\TaxonConcept;
use DOMNode;
use Illuminate\Support\Facades\DB;

class CreateCurrentProfile 
{
    public function __invoke(TaxonConcept $taxonConcept)
    {
        $profile = Profile::where('accepted_id', $taxonConcept->id)
        ->where('is_current', true)
        ->first();

        if ($profile) {
    
            $profile->profile = $this->formatProfile($taxonConcept, $profile);
            return $profile;
        }
        return null;
    }

    protected function getDOMInnerHtml(DOMNode $element)
    {
        $innerHtml = "";
        $children = $element->childNodes;
        foreach ($children as $child) {
            $innerHtml .= $element->ownerDocument->saveHTML($child);
        }
        return $innerHtml;
    }

    protected function formatProfile($taxonConcept, $profile)
    {
        $createHtmlDOMDocument = new CreateHtmlDOMDocument;
        $this->doc = $createHtmlDOMDocument($profile->profile);

        $nodeList = $this->doc->getElementsByTagName('p');
        if ($nodeList->length) {
            $prof = [];
            $classes = [];

            foreach ($nodeList as $node) {
                $prof[] = [
                    'class' => $node->getAttribute('class'),
                    'value' => $this->getDOMInnerHtml($node),
                ];
                $classes[] = $node->getAttribute('class');
            }
        }

        // Description part
        $description = [];
        $key = array_search('description', $classes);
        if ($key !== false) {
            $description[] = $prof[$key]['value'];
        }
        $key = array_search('phenology', $classes);
        if ($key !== false) {
            $description[] = $prof[$key]['value'];
        }
        if ($description) {
            $description = '<div class="description"><p>'
                    . implode(' ', $description) . '</p></div>';
        }
        else {
            $description = '';
        }

        // Distribution and habitat part
        $distribution = [];
        $getBioregionString = new GetBioregionString;
        $vicDist = $getBioregionString($taxonConcept);
        if ($vicDist) {
            $distribution[] = $vicDist;
        }
        $key = array_search('distribution_australia', $classes);
        if ($key !== false) {
            $dist = $prof[$key]['value'];
            $distribution[] = (substr($dist, -1) === '.')
                    ? $dist : $dist . '.';
        }
        $key = array_search('distribution_world', $classes);
        if ($key !== false) {
            $dist = $prof[$key]['value'];
            $distribution[] = (substr($dist, -1) === '.')
                    ? $dist : $dist . '.';
        }
        $key = array_search('habitat', $classes);
        if ($key !== false) {
            $dist = $prof[$key]['value'];
            $distribution[] = (substr($dist, -1) === '.')
                    ? $dist : $dist . '.';
        }
        if ($distribution) {
            $distribution = '<div class="distribution-habitat"><p>'
                    . implode(' ', $distribution) . '</p></div>';
        }
        else {
            $distribution = '';
        }

        // Notes part
        $notes = [];
        $keys = array_keys($classes, 'note');
        if ($keys) {
            foreach ($keys as $key) {
                $notes[] = '<p>' . $prof[$key]['value'] . '</p>';
            }
        }
        if ($notes) {
            $notes = '<div class="notes">' . implode('', $notes) . '</div>';
        }
        else {
            $notes = '';
        }

        // Concatenate parts
        $treatment = $description . $distribution . $notes;

        // Create name links
        $scientificNameLinks = new AddScientificNameLinks;
        $genus = $this->getGenusName($taxonConcept);
        $treatment = $scientificNameLinks($treatment, $genus);
        return $treatment;
    }

    protected function getGenusName($taxonConcept)
    {
        return DB::table('taxon_names')
            ->join('taxon_concepts', 'taxon_names.id', '=', 'taxon_concepts.taxon_name_id')
            ->join('taxon_tree_def_items', 'taxon_concepts.taxon_tree_def_item_id', '=', 'taxon_tree_def_items.id')
            ->where('taxon_tree_def_items.rank_id', '>=', 220)
            ->where('taxon_concepts.guid', $taxonConcept->guid)
            ->value(DB::raw("substring(taxon_names.full_name for position(' ' in taxon_names.full_name) - 1)"));
    }
}