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

namespace App\Services;

use App\Models\TaxonBioregion;
use DOMDocument;
use DOMNode;
use Illuminate\Support\Facades\DB;

class CurrentProfileService
{
    /**
     * ID of the Taxon Concept
     *
     * @var string
     */
    protected $taxonConceptId;

    /**
     * The profile string as stored in the database
     *
     * @var string
     */
    protected $profile;

    /**
     * The DOM document of the profile
     *
     * @var \DOMDocument
     */
    protected $doc;

    /**
     * Profile to return
     *
     * @var string
     */
    protected $formattedProfile;

    var $paras = [];

    var $classes = [];

    public function __construct($taxonConceptId, $profile)
    {
        $this->taxonConceptId = $taxonConceptId;
        $this->profile = $profile;
    }


    public function formatProfile()
    {
        $this->createDOMDocument();
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
        $vicDist = $this->getBioregionString();
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
        $treatment = $this->scientificNameLinks($treatment);

        return $treatment;
    }


    public function createDOMDocument()
    {
        $this->doc = new DOMDocument('1.0', 'utf-8');
        $encodingHint = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $this->doc->loadHTML($encodingHint . $this->profile);
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

    public function parseDOMDocument()
    {
        $nodeList = $this->doc->getElementsByTagName('p');
        if ($nodeList->length) {
            foreach ($nodeList as $node) {
                $this->paras[] = [
                    'class' => trim($node->getAttribute('class'), " \n\r\t\v\x00\""),
                    'value' => trim($this->getDOMInnerHtml($node), " \n\r\t\v\x00\""),
                ];
                $this->classes[] = $node->getAttribute('class');
            }
        }
    }

    protected function getBioregionString()
    {
        $regions = TaxonBioregion::where('taxon_concept_id',
                        $this->taxonConceptId)
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

    protected function scientificNameLinks($text)
    {
        $genus = $this->getGenusName();
        preg_match_all('/<span class="scientific_name">([^<]+)<\/span>/',
                $text, $matches);
        $matches = array_map('array_unique', $matches);
        foreach ($matches[0] as $index => $value) {
            $sciName = $matches[1][$index];
            if (preg_match('/^[A-Z].+[A-Za-z]$/', $sciName)) {
                if (preg_match('/^[A-Z]\. /', $sciName) && substr($sciName, 0, 1) === substr($genus, 0, 1)) {
                    $sciName = preg_replace('/[A-Z]\./', $genus, $sciName);
                }
                $guid = $this->getScientificNameLink($sciName);
                if ($guid) {
                    $text = str_replace($value,
                            "<a href=\"/flora/taxon/{$guid}\">{$value}</a>",
                            $text);
                }
            }
        }
        return $text;
    }

    protected function getGenusName()
    {
        return DB::table('taxon_names')
            ->join('taxon_concepts', 'taxon_names.id', '=', 'taxon_concepts.taxon_name_id')
            ->join('taxon_tree_def_items', 'taxon_concepts.taxon_tree_def_item_id', '=', 'taxon_tree_def_items.id')
            ->where('taxon_tree_def_items.rank_id', '>=', 220)
            ->where('taxon_concepts.guid', $this->taxonConceptId)
            ->value(DB::raw("substring(taxon_names.full_name for position(' ' in taxon_names.full_name) - 1)"));
    }

    protected function getScientificNameLink($sciName)
    {
        return DB::table('taxon_names as tn')
                ->join('taxon_concepts as tc', 'tn.id', '=', 'tc.taxon_name_id')
                ->where('tn.full_name', $sciName)
                ->orWhereRaw("substring(tn.full_name for 1)||'. '||substring(tn.full_name from position(' ' in tn.full_name) + 1)='$sciName'")
                ->orderBy('tc.according_to_id')
                ->value('tc.guid');
    }
}

