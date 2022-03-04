<?php

namespace App\GraphQL\Queries;

use App\Services\SolariumQueryService;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Solarium\Client;

class Search
{
    /**
     * The Solarium query service
     *
     * @var \App\Services\SolariumQueryService
     */
    protected $queryService;

    /**
     * Default download fields; will be used when no field list (fl) is given
     * with the arguments
     *
     * @var array
     */
    protected $defaultDownloadFields = [
        'id',
        'taxon_rank',
        'scientific_name',
        'scientific_name_authorship',
        'taxonomic_status',
        'family',
        'occurrence_status',
        'establishment_means',
        'degree_of_establishment',
        'accepted_name_usage_id',
        'accepted_name_usage',
        'accepted_name_usage_authorship',
        'accepted_name_usage_taxon_rank',
        'name_according_to',
        'sensu',
        'threat_status',
        'profile',
        'vernacular_name'
    ];

    /**
     * Default facet fields; will be used when the facetField argument is not
     * set and 'facet' is not false
     *
     * @var array
     */
    protected $defaultFacetFields = [
        'name_type',
        'taxonomic_status',
        'taxon_rank',
        'occurrence_status',
        'establishment_means',
        'degree_of_establishment',
        'threat_status',
        'class',
        'subclass',
        'superorder',
        'order',
        'family',
        'media'
    ];


    public function __construct(Client $client)
    {
        $this->queryService = new SolariumQueryService($client);
    }


    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $params = $this->getParams($args, $resolveInfo);
        $query = $this->queryService->createSelect();
        $query = $this->queryService->setQuery($query, $params);
        $query = $this->queryService->setSort($query, $params);
        $query = $this->queryService->setCursor($query, $params);
        $query = $this->queryService->setFilters($query, $params);

        $this->queryService->setFacets($query, $params);

        return $this->queryService->getResult($query, $params);
    }

    /**
     * Gets the search parameters from the GraphQL arguments and resolve info
     *
     * @param  array<string, mixed>  $args
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return array<string, mixed>
     */
    protected function getParams($args, $resolveInfo)
    {
        $fieldSelection = $resolveInfo->getFieldSelection(2);

        $params = [];
        $params['q'] = $args['input']['q'] ?: '*:*';

        if (isset($args['input']['fq']) && $args['input']['fq']) {
            $params['fq'] = $args['input']['fq'];
        }

        if (isset($fieldSelection['docs']) && $fieldSelection['docs']) {
            $params['rows'] = isset($args['input']['rows']) ? $args['input']['rows'] : 0;

            // field list
            $fieldList = [];
            foreach ($fieldSelection['docs'] as $field => $value) {
                if ($value) {
                    $fieldList[] = Str::snake($field);
                }
            }
            $params['fl'] = $fieldList;
        }
        else {
            $params['rows'] = 0;
        }

        if (isset($args['input']['sort']) && $args['input']['sort']) {
            $params['sort'] = $args['input']['sort'];
        }

        if (isset($args['input']['page']) && $args['input']['page']) {
            $params['page'] = $args['input']['page'];
        }

        if (isset($args['input']['start'])) {
            $params['start'] = $args['input']['start'];
        }

        $params['facet'] = isset($fieldSelection['fieldFacets']) && $fieldSelection['fieldFacets'] ? true : false;
        $params['facetField'] = isset($args['input']['facetField']) && $args['input']['facetField'] ? $args['input']['facetField'] : $this->defaultFacetFields;

        if (isset($args['input']['facetSort'])) {
            $params['facetSort'] = $args['input']['facetSort'];
        }

        if (isset($args['input']['facetLimit'])) {
            $params['facetLimit'] = $args['input']['facetLimit'];
        }

        if (isset($args['input']['facetOffset'])) {
            $params['facetOffset'] = $args['input']['facetOffset'];
        }

        return $params;
    }

}
