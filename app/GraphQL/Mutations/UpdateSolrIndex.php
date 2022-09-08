<?php

namespace App\GraphQL\Mutations;

use App\Actions\BuildSolrIndexQuery;
use App\Actions\CreateGraphQLSearchResultDocument;
use App\Actions\CreateSearchResultDocument;
use App\Actions\CreateSolrDocument;
use App\Actions\CreateSolrSearchResultDocument;
use Solarium\Client;

class UpdateSolrIndex
{
    /**
     * The Solarium client
     *
     * @var Client
     */
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $buildQuery = new BuildSolrIndexQuery;
        $createSearchResultDocument = new CreateSearchResultDocument;
        $createDocument = new CreateSolrDocument;
        $createGraphQLSearchResultDocument = 
                new CreateGraphQLSearchResultDocument;

        $query = $buildQuery();

        $result = $query->where('t.guid', $args['id'])->first();
        $searchResultDocument = $createSearchResultDocument($result);

        $updateQuery = $this->client->createUpdate();

        $doc = $createDocument($updateQuery, $searchResultDocument);

        $updateQuery->addDocument($doc, true);
        $updateQuery->addCommit();
        $this->client->update($updateQuery);

        return $createGraphQLSearchResultDocument($searchResultDocument);
    }
}
