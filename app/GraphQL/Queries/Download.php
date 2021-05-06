<?php

namespace App\GraphQL\Queries;

use App\Services\SolariumQueryService;
use Illuminate\Support\Str;
use Solarium\Client;

/**
 * Undocumented class
 */
class Download
{

    /**
     * The Solarium Query Service
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
     * 
     */
    public function __construct(Client $client)
    {
        $this->queryService = new SolariumQueryService($client);
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $params = $this->getParams($args);
        $query = $this->queryService->createSelect();
        $query = $this->queryService->setQuery($query, $params);
        $query = $this->queryService->setSort($query, $params);
        $query->setStart(0)->setRows(11000);
        $query = $this->queryService->setFilters($query, $params);
        return $this->queryService->getDownloadResult($query, $params);
    }

    /**
     * Gets the search parameters from the GraphQL arguments and resolve info
     *
     * @param  array<string, mixed>  $args
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return array<string, mixed>
     */
    protected function getParams($args)
    {
        $params = [];
        $params['q'] = $args['input']['q'] ?: '*:*';

        if (isset($args['input']['fq']) && $args['input']['fq']) {
            $params['fq'] = $args['input']['fq'];
        }

        // field list
        $params['fl'] = implode(',', isset($args['input']['fl']) ? $args['input']['fl'] : $this->defaultDownloadFields);

        if (isset($args['input']['sort']) && $args['input']['sort']) {
            $params['sort'] = $args['input']['sort'];
        }

        return $params;
    }
}
