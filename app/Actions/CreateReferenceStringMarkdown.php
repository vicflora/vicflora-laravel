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

class CreateReferenceStringMarkdown
{
    public function __invoke(Reference $reference)
    {
        $type = $reference->referenceType->name;
        $referenceString = '';
        switch ($type) {
            case 'Journal':
            case 'Series':
                $referenceString .= $reference->title;
                break;

            case 'Book':
            case 'Report':
            case 'AudioVisualDocument':
                $referenceString .= '**' . $reference->author->name
                        . ' (' . $reference->publication_year . ').** '
                        . str_replace('~', '*', $reference->title);

                if ($reference->edition) {
                    $referenceString .= ', edn ' . $reference->edition;
                }

                $referenceString .= '. ';

                if ($reference->publisher) {
                    $referenceString .= ' ' . $reference->publisher;
                    if ($reference->place_of_publication) {
                        $referenceString .= ', ' . $reference->place_of_publication;
                    }
                    $referenceString .= '.';
                }
                break;

            case 'Article':
                $referenceString .= '**' . $reference->author->name
                        . ' (' . $reference->publication_year . ')**. '
                        . str_replace('~', '*', $reference->title) . '. ';
                $referenceString .= '*' . $reference->parent->title . '*';
                if ($reference->volume) {
                    $referenceString .= ' **' . $reference->volume . '**';
                    if ($reference->issue) {
                        $referenceString .= '(' . $reference->issue . ')';
                    }
                    if ($reference->page_start) {
                        $referenceString .= ': ' .
                                $reference->page_start . '–' . $reference->page_end;
                    }
                    elseif ($reference->pages) {
                        $referenceString .= ': ' . $reference->pages;
                    }
                }
                elseif ($reference->number) {
                    $referenceString .= ' ' . $reference->number;
                }
                $referenceString .= '.';
                break;

            case 'Chapter':
                $referenceString .= '**' . $reference->author->name
                        . ' (' . $reference->publication_year . ')**. '
                        . str_replace('~', '*', $reference->title) . '. ';

                $referenceString .= 'In: ' . $reference->parent->author->name
                        . ', *&zwj;' . $reference->parent->title . '&zwj;*';


                if ($reference->parent->edition) {
                    $referenceString .= ', edn ' . $reference->parent->edition;
                }

                $referenceString .= ', pp. ' . $reference->page_start
                        . '–' . $reference->page_end
                        . '.';
                        
                if ($reference->parent->publisher) {
                    $referenceString .= ' ' . $reference->parent->publisher;
                    if ($reference->parent->place_of_publication) {
                        $referenceString .= ', '
                            . $reference->parent->place_of_publication;
                    }
                    $referenceString .= '.';
                }
                break;

            case 'Protologue':
                if ($reference->author_id) {
                    $referenceString .= 'in ' . $reference->author->name . ', ';
                }
                $referenceString .= '*' . $reference->title . '*';
                if ($reference->volume) {
                    $referenceString .= ' **' . $reference->volume . '**';
                }
                if ($reference->issue) {
                    $referenceString .= '(' . $reference->issue . ')';
                }
                if ($reference->pages) {
                    $referenceString .= ': ' . $reference->pages;
                }
                if ($reference->publication_year) {
                    $referenceString .= ' (' . $reference->publication_year . ')';
                }
                break;

            default:
                break;
        }
        return $referenceString;
    }
}

