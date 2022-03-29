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

use App\Models\Reference;

class CreateCitation
{
    public function __invoke(Reference $reference)
    {
        $getContributorString = new GetContributorString;

        $type = $reference->referenceType->name;
        $citation = '';
        switch ($type) {
            case 'Journal':
                case 'Series':
                    $citation .= $reference->title;
                    break;

                case 'Book':
                case 'Report':
                case 'AudioVisualDocument':
                    $citation .= $getContributorString($reference)
                            . ' (' . $reference->publication_year . '). '
                            . $reference->title . '. ';
                    if ($reference->publisher) {
                        $citation .= ' ' . $reference->publisher;
                        if ($reference->place_of_publication) {
                            $citation .= ', ' . $reference->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Article':
                    $citation .= $getContributorString($reference)
                            . ' (' . $reference->publication_year . '). '
                            . $reference->title . '. ';
                    $citation .= $reference->parent->title;
                    if ($reference->volume) {
                        $citation .= ' ' . $reference->volume;
                        if ($reference->issue) {
                            $citation .= '(' . $reference->issue . ')';
                        }
                        if ($reference->page_start) {
                            $citation .= ': ' .
                                    $reference->page_start . '–' . $reference->page_end;
                        }
                        elseif ($reference->pages) {
                            $citation .= ': ' . $reference->pages;
                        }
                    }
                    elseif ($reference->number) {
                        $citation .= ' ' . $reference->number;
                    }
                    $citation .= '.';
                    break;

                case 'Chapter':
                    $citation .= $getContributorString($reference)
                            . ' (' . $reference->publication_year . '). '
                            . $reference->title . '. ';
                    $citation .= 'In: ' . $getContributorString($reference->parent)
                            . ', ' . $reference->parent->title
                            . ', pp. ' . $reference->page_start
                            . '–' . $reference->page_end
                            . '.';
                    if ($reference->parent->publisher) {
                        $citation .= ' ' . $reference->parent->publisher;
                        if ($reference->parent->place_of_publication) {
                            $citation .= ', '
                                . $reference->parent->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Protologue':
                    if ($reference->author_id) {
                        $citation .= 'in ' . $reference->author->name . ', ';
                    }
                    $citation .= $reference->title;
                    if ($reference->volume) {
                        $citation .= ' ' . $reference->volume;
                    }
                    if ($reference->issue) {
                        $citation .= '(' . $reference->issue . ')';
                    }
                    $citation .= ': ' . $reference->pages
                            . ' (' . $reference->publication_year . ')';
                    break;

                default:
                    break;
        }
        return $citation;
    }
}

