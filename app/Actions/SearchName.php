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

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;
use GraphQL\Variable;

class SearchName {
    
    public function __invoke($name)
    {
        $gql = (new Query('search'))
        ->setVariables([new Variable('input', 'SearchInput', true)])
        ->setArguments(['input' => '$input'])
        ->setSelectionSet(
            [
                (new Query('docs'))->setSelectionSet([
                    'id',
                    'scientificName',
                    'scientificNameAuthorship',
                    'acceptedNameUsageId',
                ]),
            ]
        );

        $client = new Client('http://nginx:81/graphql');

        try {
            $input = [
                'q' => "scientific_name:\"$name\""
            ];
            $result = $client->runQuery($gql, true, ['input' => $input]);
        }
        catch (QueryError $exception) {
            return response($exception->getErrorDetails());
            exit;
        }

        return $result->getData();
    }   
}