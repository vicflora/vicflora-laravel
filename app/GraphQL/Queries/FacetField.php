<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Str;
use Solarium\Client;

class FacetField
{
    /**
     * The Solarium client
     *
     * @var \Solarium\Client
     */
    protected $client;

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
        $query = $this->client->createSelect();
        $query->setQueryDefaultField('text')
                ->setQuery((isset($args['input']['q'])) ? $args['input']['q'] : '*:*');

        if (isset($args['input']['fq'])) {
            if (is_array($args['input']['fq'])) {
                foreach ($args['input']['fq'] as $index => $fq) {
                    $query->createFilterQuery('fq_' . $index)->setQuery($fq);
                }
            }
            else {
                $query->createFilterQuery('fq_' . 0)->setQuery($args['input']['fq']);
            }
        }

        $facetSet = $query->getFacetSet();
        $facetSet->createFacetField($args['input']['field'])
                ->setField($args['input']['field'])
                ->setSort(isset($args['input']['sort']) && $args['input']['sort']
                         ? $args['input']['sort'] : 'count')
                ->setMissing(true)
                ->setMincount(1)
                ->setLimit($args['input']['limit'])
                ->setOffset($args['input']['offset']);

        $resultSet = $this->client->select($query);
        $facetField = [
            'fieldName' => Str::camel($args['input']['field']),
            'facets' => [],
        ];
        $facet = $resultSet->getFacetSet()->getFacet($args['input']['field']);
        foreach ($facet as $value => $count) {
            if ($value != "") {
                $facetField['facets'][] = [
                    'value' => $value,
                    'count' => $count,
                    'fq' => $args['input']['field'] . ':' . str_replace(' ', "\ ", $value)
                ];
            }
            elseif ($count > 0) {
                $facetField['facets'][] = [
                    'value' => '(blank)',
                    'count' => $count,
                    'fq' => '-' . $args['input']['field'] . ':*'
                ];
            }
        }
        return $facetField;
    }
}
