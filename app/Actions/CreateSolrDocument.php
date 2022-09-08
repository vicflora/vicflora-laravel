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

use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;

class CreateSolrDocument {
    
    public function __invoke(Query $updateQuery, array $data): Document
    {
        $doc = $updateQuery->createDocument();

        foreach ($data as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $doc->addField($key, $item);
                    }
                }
                else {
                    $doc->setField($key, $value);
                }
            }

        }


        return $doc;
    }
}