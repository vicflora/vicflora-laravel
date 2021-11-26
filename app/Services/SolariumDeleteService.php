<?php

/*
 * Copyright 2018 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Services;

use Solarium\Client;

/**
 * Description of SolrDeleteService
 *
 * @author Niels Klazenga <Niels.Klazenga@rbg.vic.gov.au>
 */
class SolariumDeleteService 
{
    
    /**
     * The SOLR client
     *
     * @var \Solarium\Client
     */
    public $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    public function deleteByQuery($query)
    {
        // get an update query instance
        $update = $this->client->createUpdate();

        // add the delete query and a commit command to the update query
        $update->addDeleteQuery($query);
        $update->addCommit();

        // execute the query and returns the result
        $result = $this->client->update($update);
        return $result->getStatus();
    }
    
    public function deleteById($id)
    {
        $update = $this->client->createUpdate()->addDeleteById($id)->addCommit();
        $result = $this->client->update($update);
        return $result->getStatus();
    }

}

