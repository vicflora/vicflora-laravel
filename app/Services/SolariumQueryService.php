<?php

/*
 * Copyright 2021 Royal Botanic Gardens Victoria.
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

use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Str;
use Solarium\Client;

/**
 * Description of SolariumQueryService
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class SolariumQueryService {

    /**
     * The SOLR client
     *
     * @var \Solarium\Client
     */
    public $client;

    /**
     * 
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    /**
     * Creates a Solarium query
     *
     * @return \Solarium\Core\Query\AbstractQuery|\Solarium\QueryType\Select\Query\Query
     */
    public function createSelect()
    {
        return $this->client->createSelect();
    }

    /**
     * Sets the query string(s) to search on and the fields to return
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param array<string, mixed> $params
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function setQuery($query, $params)
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
    public function setSort($query, $params)
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
    public function setCursor($query, $params)
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
    public function setFilters($query, $params)
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
        if (!(isset($params['facet']) && $params['facet'] == "false")) {
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
        }
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
    public function getResult($query, $params)
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
    public function getDocs($resultSet)
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
    public function getFacetFieldResults($resultSet, $params)
    {
        //if (!(isset($params['facet']) && $params['facet'] == "false")) {
            $facetFields = [];
            $fields = $this->getFacetFields($params);
            $facetSet = $resultSet->getFacetSet();
            foreach ($fields as $field) {
                $facetField = [
                    'fieldName' => Str::camel($field),
                    'fieldLabel' => Str::ucfirst(Str::replaceFirst('_', ' ', $field)),
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
    public function pagination($result, $params)
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

    /**
     * Create the search result array
     *
     * @param \Solarium\QueryType\Select\Query\Query $query
     * @param array<string, mixed> $params
     * @return array<mixed>
     */
    public function getDownloadResult($query, $params)
    {
        // Result
        $resultSet = $this->client->select($query);
        $docs = $this->getDownloadDocs($resultSet);
        $response = [];
        $response['data'] = $this->str_putcsv($docs, $params);
        return $response;
    }

    /**
     * Gets the documents
     *
     * @param \Solarium\QueryType\Select\Result\Result $resultSet
     * @return array<\Solarium\QueryType\Select\Result\Document>
     */
    protected function getDownloadDocs($resultSet)
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
     * Convert a multi-dimensional, associative array to CSV data
     * 
     * @param  array $data the array of data
     * @return string       CSV text
     */
    protected function str_putcsv($data, $params) {

        $fields = explode(',', $params['fl']);
            # Generate CSV data from array
            $fh = fopen('php://temp', 'rw'); 

            # write out the headers
            fputcsv($fh, $fields);
            
            # write out the data
            foreach ( $data as $row ) {
                $values = [];
                foreach ($fields as $field) {
                    if (isset($row[Str::camel($field)])) {
                        if (is_array($row[Str::camel($field)])) {
                            $values[] = implode(' | ', $row[Str::camel($field)]);
                        }
                        else {
                            $values[] = $row[Str::camel($field)];
                        }
                    }
                    else {
                        $values[] = null;
                    }
                }
                fputcsv($fh, $values);
            }
            rewind($fh);
            $csv = stream_get_contents($fh);
            fclose($fh);

            return $csv;
    }

}
