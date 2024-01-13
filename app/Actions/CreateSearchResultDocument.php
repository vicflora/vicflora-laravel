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

use stdClass;

class CreateSearchResultDocument {

    /**
     * Convert single row from SOLR query to Search Result Document associate
     * array that can in turn be converted to a SOLR document. The resulting
     * array can also be converted to a GraphQL SearchResultDocument type.
     *
     * @param stdClass $data
     * @return array<string|mixed>
     */
    public function __invoke(stdClass $data): array
    {
        $doc = [];
        $doc['id'] = $data->id;
        $doc['scientific_name_id'] = $data->name_id;
        $doc['scientific_name'] = $data->full_name;
        $doc['scientific_name_authorship'] = $data->authorship;
        $doc['name_type'] = $data->name_type;

        $doc['name_published_in'] = $data->citation;
        $doc['name_published_in_id'] = $data->published_in_id;
        $doc['name_published_in_year'] = $data->publication_year;

        $doc['name_according_to'] = null;
        $doc['name_according_to_id'] = null;
        if ($data->according_to_id) {
            $doc['name_according_to'] = $this->getShortCitation(
                    $data->according_to_author,
                    $data->according_to_publication_year);
            $doc['name_according_to_id'] = $data->according_to_id;
        }

        $doc['kingdom'] = $data->kingdom;
        $doc['phylum'] = $data->phylum;
        $doc['class'] = $data->class;
        $doc['order'] = $data->order;
        $doc['family'] = $data->family;
        $doc['genus'] = $data->genus;
        $doc['species'] = $data->species;

        $doc['generic_name'] = $data->generic_name;
        $doc['specific_epithet'] = $data->specific_epithet;
        $doc['infraspecific_epithet'] = $data->infraspecific_epithet;

        $doc['parent_name_usage_id'] = $data->parent_id;
        $doc['parent_name_usage'] = $data->parent_name;

        $doc['accepted_name_usage_id'] = $data->accepted_name_id;
        $doc['accepted_name_usage'] = $data->accepted_name;
        $doc['accepted_name_usage_authorship'] =
                $data->accepted_name_authorship;
        $doc['accepted_name_usage_taxon_rank'] = $data->accepted_name_rank;

        $doc['taxonomic_status'] = $data->taxonomic_status;
        $doc['occurrence_status'] = $data->occurrence_status;
        $doc['establishment_means'] = $data->establishment_means;
        $doc['degree_of_establishment'] = $data->degree_of_establishment;
        $doc['endemic'] = $data->is_endemic;
        $doc['has_introduced_occurrences'] = $data->has_introduced_occurrences;

        $doc['epbc'] = $data->epbc;
        $doc['ffg'] = $data->ffg;
        $doc['vic_advisory'] = $data->vic_advisory;

        $doc['taxon_remarks'] = $data->remarks;
        $doc['preferred_vernacular_name'] = $data->preferred_vernacular_name;
        $doc['vernacular_name'] = json_decode($data->vernacular_names);

        $doc['end_or_higher_taxon'] = $data->end_or_higher_taxon;

        $doc['taxon_rank'] = $data->rank;

        $doc['bioregion'] = json_decode($data->bioregions);
        $doc['local_government_area'] =
                json_decode($data->local_government_areas);
        $doc['park_or_reserve'] = json_decode($data->park_reserves);
        $doc['registered_aboriginal_party'] =
                json_decode($data->registered_aboriginal_parties);

        $doc['media'] = null;
        if ($data->media) {
            $doc['media'] = explode(' | ', $data->media);
        }

        $doc['description'] = $data->description;
        $doc['publication_status'] = $data->publication_status;
        $doc['created'] = $data->created_at;
        $doc['modified'] = $data->updated_at;
        $doc['created'] = $data->created_at;
        $doc['modified'] = $data->updated_at;
        $doc['last_edit'] = $data->last_edit;
        $doc['created_year'] = $data->created_year;
        $doc['created_year_month'] = $data->created_year_month;
        $doc['changed_year'] = $data->changed_year;
        $doc['changed_year_month'] = $data->changed_year_month;
        $doc['profile_created'] = $data->profile_created;
        $doc['profile_created_year'] = $data->profile_created_year;
        $doc['profile_created_year_month'] = $data->profile_created_year_month;
        $doc['profile_updated'] = $data->profile_updated;
        $doc['profile_updated_year'] = $data->profile_updated_year;
        $doc['profile_updated_year_month'] = $data->profile_updated_year_month;
        return $doc;
    }

    private function getShortCitation($author, $year) {
        $authors = explode(' | ', $author);
        $ret = $this->getAuthorSurname($authors[0]);
        switch (count($authors)) {
            case 1:
                $ret .= '';
                break;

            case 2:
                $ret .= ' & ' . $this->getAuthorSurname($authors[1]);
                break;

            default:
                $ret .= ' et al.';
                break;
        }
        return $ret . ' ' . $year;
    }

    private function getAuthorSurname($author) {
        $bits = explode(', ', $author);
        return $bits[0];
    }
}
