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

class AddScientificNameLinks 
{
    public function __invoke($text, $genus)
    {
        $getScientificNameLink = new GetScientificNameLink;

        preg_match_all('/<span class="scientific_name">([^<]+)<\/span>/',
                $text, $matches);
        $matches = array_map('array_unique', $matches);
        foreach ($matches[0] as $index => $value) {
            $sciName = $matches[1][$index];
            if (preg_match('/^[A-Z].+[A-Za-z]$/', $sciName)) {
                if (preg_match('/^[A-Z]\. /', $sciName) && substr($sciName, 0, 1) === substr($genus, 0, 1)) {
                    $sciName = preg_replace('/[A-Z]\./', $genus, $sciName);
                }
                $guid = $getScientificNameLink($sciName);
                if ($guid) {
                    $text = str_replace($value,
                            "<a href=\"/flora/taxon/{$guid}\">{$value}</a>",
                            $text);
                }
            }
        }
        return $text;
    }
}