<?php

namespace App\Services;

use GuzzleHttp\Client;

class CantoImageService
{

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('IMAGE_API_BASE_URL')
        ]);
    }

    /**
     * Get images
     *
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function getImages(int $perPage=50, int $page=1): array
    {
        $response = $this->client->get('images', [
                'query' => [
                    'perPage' => $perPage,
                    'page' => 1
                ]
            ]);

        if ($response->getStatusCode() == '200') {
            $json = json_decode($response->getBody(), true);
            if ($json['data']) {
                foreach ($json['data'] as $item) {
                    $images[] = $this->createImageRecord($item);
                }
                return [
                    'data' => $images,
                    'paginatorInfo' => $this->createPaginatorInfo($json)
                ];
            }
            return [];
        }
        return [];
    }

    /**
     * Get image
     *
     * @param string $id
     * @return array|null
     */
    public function getImage(string $id): ?array
    {
        $response = $this->client->get('images/' . $id);

        if ($response->getStatusCode() == '200') {
            $json = json_decode($response->getBody(), true);
            if ($json['data']) {
                return $this->createImageRecord($json['data'][0]);
            }
            return null;
        }
        return null;
    }

    /**
     * Find images for taxon
     *
     * @param string $taxonConceptId
     * @param mixed $first
     * @param mixed $page
     * @return array
     */
    public function findByTaxon(
            string $taxonConceptId,
            ?int $first=12,
            ?int $page=1): array
    {
        $response = $this->client->get('images/findByTaxon', [
                'query' => [
                    'taxonConceptId' => $taxonConceptId,
                    'perPage' => $first,
                    'page' => $page
                ]
            ]);

        if ($response->getStatusCode() == '200') {
            $json = json_decode($response->getBody(), true);
            $images = [];
            if ($json['data']) {
                foreach ($json['data'] as $item) {
                    $images[] = $this->createImageRecord($item);
                }
                return [
                    'data' => $images,
                    'paginatorInfo' => $this->createPaginatorInfo($json)
                ];
            }
            return [];
        }
        return [];
    }

    /**
     * Get hero image for taxon
     *
     * @param mixed $taxonConceptId
     * @return array|null
     */
    public function heroImage($taxonConceptId): ?array
    {
        $response = $this->client->get('images/findByTaxon', [
                'query' => [
                    'taxonConceptId' => $taxonConceptId,
                    'perPage' => 1,
                    'page' => 1
                ]
            ]);

        if ($response->getStatusCode() == '200') {
            $json = json_decode($response->getBody(), true);
            if ($json['data']) {
                return $this->createImageRecord($json['data'][0]);
            }
            return null;
        }
        return null;
    }

    /**
     * Convert API result to GraphQL Image (array)
     *
     * @param mixed $data
     * @return array
     */
    private function createImageRecord($data)
    {
        return [
            'id' => $data['id'],
            'caption' => $this->createCaption($data),
            'catalogNumber' => $data['catalogNumber'],
            'copyrightOwner' => $data['copyrightOwner'],
            'country' => $data['country'],
            'creationDate' => $data['creationDate'],
            'creator' => $data['creator'],
            'cantoFileName' => $data['cantoFileName'],
            'decimalLatitude' => $data['decimalLatitude'],
            'decimalLongitude' => $data['decimalLongitude'],
            'heroImage' => $data['isHero'],
            'license' => $data['license'],
            'locality' => $data['locality'],
            'modified' => $data['modified'],
            'pixelXDimension' => (int) $data['pixelXDimension'],
            'pixelYDimension' => (int) $data['pixelYDimension'],
            'rating' => $data['rating'],
            'recordedBy' => $data['recordedBy'],
            'recordNumber' => $data['recordNumber'],
            'rights' => $data['rights'],
            'scientificName' => $data['scientificName'],
            'originalScientificName' => $data['originalScientificName'],
            'source' => $data['source'],
            'stateProvince' => $data['stateProvince'],
            'subjectCategory' => $data['subjectCategory'],
            'subtype' => $data['subType'],
            'title' => $data['title'],
            'type' => $data['type'],
            'thumbnailUrl' => $data['thumbnailUrl'],
            'previewUrl' => $data['previewUrl'],
            'highestResUrl' => $data['highestResUrl'],
            'createdAt' => $data['created_at'],
            'updatedAt' => $data['updated_at'],
        ];
    }

    /**
     * Convert API pagination to Lighthouse PaginatorInfo (array)
     *
     * @param mixed $data
     * @return array
     */
    private function createPaginatorInfo($data)
    {
        return [
            'count' => $data['to'] - $data['from'] + 1,
            'currentPage' => $data['current_page'],
            'firstItem' => $data['from'],
            'hasMorePages' => $data['total'] > $data['to'],
            'lastItem' => $data['to'],
            'lastPage' => $data['last_page'],
            'perPage' => $data['per_page'],
            'total' => $data['total'],
        ];
    }

    /**
     * Create caption from image metadata
     *
     * @param array $image
     * @return string
     */
    private function createCaption(array $image): string
    {
        $scientificName = "<i>{$image['scientificName']}</i>";
        if ($image['originalScientificName']) {
            $scientificName .= " (as <i>{$image['originalScientificName']}</i>)";
        }
        elseif ($image['taxon']['taxonomicStatus'] == 'synonym' && $image['taxon']['acceptedName'] != $image['scientificName']) {
            $scientificName = "<i>{$image['taxon']['acceptedName']}</i> (as <i>{$image['scientificName']}</i>)";
        }
        $search = [' subsp. ', ' var. ', ' f. '];
        $replace = [
            '</i> subsp. <i>',
            '</i> var. <i>',
            '</i> f. <i>'
        ];
        $scientificName = str_replace($search, $replace, $scientificName);

        if (substr($image['license'], 0, 5) === 'CC BY') {
            $bits = explode(' ', $image['license']);
            $url = 'https://creativecommons.org/licenses/';
            $url .= strtolower($bits[1]);
            $url .= (isset($bits[2])) ? '/' . $bits[2] : '/4.0';
            if (isset($bits[3])) {$url .= '/' .strtolower ($bits[3]);}
            $license = "<a href='$url'>{$image['license']}</a>";
        }
        elseif ($image['license'] == 'All rights reserved') {
            $license = 'all rights reserved';
        }
        elseif ($image['subjectCategory'] == 'Flora of the Otway Plain and Ranges Plate') {
            $license = 'not to be reproduced without prior permission from CSIRO Publishing.';
        }
        else {
            $license = "<a href='https://creativecommons.org/licenses/by-nc-sa/4.0'>CC BY-NC-SA 4.0</a>";
        }

        $caption = $scientificName;
        if ($image['caption']) {
            $caption .= ". {$image['caption']}";
        }
        $caption .= '<br/>';
        if ($image['source'] && $image['subjectCategory'] !== 'Flora of the Otway Plain and Ranges Plate') {
            $caption .= "<b>Source:</b> {$image['source']}<br/>";
        }
        $caption .= $image['subType'] === 'Illustration' ? '<b>Illustration:</b> ' : '<b>Photo:</b> ';
        $caption .= $image['creator'];

        if (strpos($license, 'CC BY') !== false) {
            $caption .= ", {$license}";
        }
        else {
            $caption .= ', &copy; ' . date('Y');
            $copyrightOwner = $image['copyrightOwner'];
            if ($copyrightOwner === 'Royal Botanic Gardens Victoria') {
                $copyrightOwner = 'Royal Botanic Gardens Board';
            }
            if ($copyrightOwner) {
                $caption .= " {$copyrightOwner}";
            }
            else {
                $caption .= " Royal Botanic Gardens Board";
            }
            if ($image['subjectCategory'] == 'Flora of the Otway Plain and Ranges Plate') {
                $caption .= ". {$image['rights']}. Not to be reproduced without
                prior permission from CSIRO Publishing.";
            }
            else {
                $caption .= ", {$license}";
            }
        }

        return $caption;
    }
}
