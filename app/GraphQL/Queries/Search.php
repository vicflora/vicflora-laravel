<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Solarium\Client;

class Search
{

    /**
     * The SOLR client
     *
     * @var \Solarium\Client
     */
    protected $client;

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
        'threat_status',
        'class',
        'subclass',
        'superorder',
        'order',
        'family',
        'ibra_7_subregion',
        'nrm_region',
        'media'
    ];

    /**
     * 
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $params = $this->getParams($args, $resolveInfo);
        $query = $this->client->createSelect();
        $query = $this->setQuery($query, $params);
        $query = $this->setSort($query, $params);
        $query = $this->setCursor($query, $params);
        $query = $this->setFilters($query, $params);

        // $query = $this->setFacets($query, $params);
        $facetFields = $this->getFacetFields($params);
        $facetSet = $query->getFacetSet();
        foreach ($facetFields as $field) {
            $facetField = $facetSet->createFacetField($field)
                    ->setField($field)
                    ->setMissing(true)
                    ->setMincount(1);
            if (isset($params['facetSort'])) {
                $facetField->setSort($params['facetSort']);
            }
            if (isset($params['facetLimit'])
                    && is_numeric($params['facetLimit'])) {
                $facetField->setLimit($params['facetLimit']);
            }
            if (isset($params['facetOffset'])
                    && is_numeric($params['facetOffset'])) {
                $facetField->setOffset($params['facetOffset']);
            }
        }

        return $this->getResult($query, $params);
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

    /**
     * Sets the query string(s) to search on and the fields to return
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param array<string, mixed> $params
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function setQuery($query, $params)
    {
        return $query
                ->setQueryDefaultField('text')
                ->setQuery((isset($params['q'])) ? $params['q'] : '*:*')
                ->setFields(isset($params['fl']) ?
                    $params['fl'] : $this->defaultDownloadFields);
    }

    /**
     * Sets the sorting of the search results
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param array<string, mixed> $params
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function setSort($query, $params)
    {
        $sortOrder = 'asc';
        if (isset ($params['sort'])) {
            if (!is_array($params['sort'])) {
                $params['sort'] = [$params['sort']];
            }
            foreach ($params['sort'] as $sort) {
                if (strpos($sort, ' ')) {
                    if (substr($sort, strpos($sort, ' ') + 1) == 'desc') {
                        $sortOrder = 'desc';
                    };
                    $sort = substr($sort, 0, strpos($sort, ' '));
                }
                $query->addSort($sort, $sortOrder);
            }
        }
        else {
            $query->addSort('scientific_name', $sortOrder);
        }
        return $query;
    }

    /**
     * Sets the cursor
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param array<string, mixed> $params
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function setCursor($query, $params)
    {
        $rows = 20;
        if (isset($params['rows'])) {
            $rows = $params['rows'];
        }
        $start = 0;
        if (isset($params['page'])) {
            $start = ($params['page'] - 1) * $rows;
        }
        elseif (isset($params['start'])) {
            $start = $params['start'] - ($params['start'] % $rows);
        }
        return $query->setStart($start)->setRows($rows);
    }

    /**
     * Set the filter queries (fq)
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param [type] $params
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function setFilters($query, $params)
    {
        if (isset($params['fq'])) {
            if (is_array($params['fq'])) {
                foreach ($params['fq'] as $index => $fq) {
                    $query->createFilterQuery('fq_' . $index)->setQuery($fq);
                }
            }
            else {
                $query->createFilterQuery('fq_' . 0)->setQuery($params['fq']);
            }
        }
        return $query;
    }

    /**
     * Set facet fields
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param array<string, mixed> $params
     * @return void
     */
    public function setFacets($query, $params)
    {
        //if (!(isset($params['facet']) && $params['facet'] == "false")) {
            $facetFields = $this->getFacetFields($params);
            $facetSet = $query->getFacetSet();
            foreach ($facetFields as $field) {
                $facetField = $facetSet->createFacetField($field)
                        ->setField($field)
                        ->setMissing(true)
                        ->setMincount(1);
                if (isset($params['facetSort'])) {
                    $facetField->setSort($params['facetSort']);
                }
                if (isset($params['facetLimit'])
                        && is_numeric($params['facetLimit'])) {
                    $facetField->setLimit($params['facetLimit']);
                }
                if (isset($params['facetOffset'])
                        && is_numeric($params['facetOffset'])) {
                    $facetField->setOffset($params['facetOffset']);
                }
            }
        //}
        return $query;
    }

    /**
     * Get the facet fields. If the facet fields are not set in the parameters
     * a default list will be used
     *
     * @param array<string, mixed> $params
     * @return array<string>
     */
    public function getFacetFields($params)
    {
        if (isset($params['facetField']) &&
                is_array($params['facetField'])) {
            $facetFields = $params['facetField'];
        }
        elseif(isset($params['facetField'])) {
            $facetFields = [$params['facetField']];
        }
        else {
            $facetFields = $this->defaultFacetFields;
        }
        return $facetFields;
    }

    /**
     * Create the search result array
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param array<string, mixed> $params
     * @return array<mixed>
     */
    protected function getResult($query, $params)
    {
        // Result
        $resultSet = $this->client->select($query);
        $response = [];
        $response['meta']['params'] = $params;
        $response['meta']['query'] = Query::build($params);
        if ($params['rows']) {
            $response['meta']['pagination'] = $this->pagination($resultSet,
                    $params);
            $response['docs'] = $this->getDocs($resultSet);
        }
        $facetFields = $this->getFacetFieldResults($resultSet, $params);
        if ($facetFields) {
            $response['facetFields'] = $facetFields;
        }
        return $response;
    }

    /**
     * Gets the documents
     *
     * @param \Solarium\QueryType\Select\Result\Result $resultSet
     * @return array<\Solarium\QueryType\Select\Result\Document>
     */
    protected function getDocs($resultSet)
    {
        $docs = [];
        foreach ($resultSet as $document) {
            $doc = [];
            foreach ($document as $field => $value) {
                $label = Str::camel($field);
                $doc[$label] = $value;
            }
            $docs[] = $doc;
        }
        return $docs;
    }

    /**
     * Gets the facets
     *
     * @param \Solarium\QueryType\Select\Result\Result $resultSet
     * @param array<string, mixed> $params
     * @return array<\Solarium\Component\Facet\Field>
     */
    protected function getFacetFieldResults($resultSet, $params)
    {
        //if (!(isset($params['facet']) && $params['facet'] == "false")) {
            $facetFields = [];
            $fields = $this->getFacetFields($params);
            $facetSet = $resultSet->getFacetSet();
            foreach ($fields as $field) {
                $facetField = [
                    'fieldName' => Str::camel($field),
                    'facets' => [],

                ];

                $facet = $facetSet->getFacet($field);
                foreach ($facet as $value => $count) {
                    if ($value != "") {
                        $facetField['facets'][] = [
                            'value' => $value,
                            'count' => $count,
                            'fq' => $field . ':' . str_replace(' ', "\ ", $value)
                        ];
                    }
                    elseif ($count > 0) {
                        $facetField['facets'][] = [
                            'value' => '(blank)',
                            'count' => $count,
                            'fq' => '-' . $field . ':*'
                        ];
                    }
                }
                $facetFields[] = $facetField;
            }
            return $facetFields;
        //}
    }

    /**
     * Creates the pagination array
     *
     * @param [type] $result
     * @param [type] $params
     * @return void
     */
    protected function pagination($result, $params)
    {
        $total = $result->getNumFound();
        $perPage = 20;
        if (isset($params['rows'])) {
            $perPage = $params['rows'];
        }
        $page = 1;
        if (isset($params['page'])) {
            $page = $params['page'];
        }
        elseif (isset($params['start'])) {
            $page = floor($params['start'] / $perPage);
        }
        $numPages = ceil($total / $perPage);
        $pagination = [
            'count' => ($page < $numPages) ? $perPage : $total % $perPage,
            'currentPage' => $page,
            'firstItem' => ($page - 1) * $perPage,
            'hasMorePages' => ($page * $perPage) < $total ? true : false,
            'lastItem' => ($page * $perPage) < $total ? $page * $perPage : $total,
            'lastPage' => $numPages,
            'perPage' => $perPage,
            'total' => $total,
        ];
        return $pagination;
    }
}
