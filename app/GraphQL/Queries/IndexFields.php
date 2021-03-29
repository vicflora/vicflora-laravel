<?php

namespace App\GraphQL\Queries;

class IndexFields
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            ["name" => "id", "type" => "string", "indexed" => true, "stored" => true, "required" => true, "multiValued" => false],
            ["name" => "taxon_rank", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "end_or_higher_taxon", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "scientific_name_id", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "scientific_name", "type" => "string_ci", "indexed" => true, "stored" => true],
            ["name" => "scientific_name_authorship", "type" => "text_general", "indexed" => true, "stored" => true],
            ["name" => "name_type", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "name_published_in_id", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "name_published_in", "type" => "text_general", "indexed" => true, "stored" => true],
            ["name" => "name_published_in_year", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "sensu", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "name_according_to", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "kingdom", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "phylum", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "class", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "subclass", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "superorder", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "order", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "family", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "genus", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "specific_epithet", "type" => "string_ci", "indexed" => true, "stored" => true],
            ["name" => "infraspecific_epithet", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "parent_name_usage_id", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "parent_name_usage", "type" => "string_ci", "indexed" => true, "stored" => true],
            ["name" => "parent_name_usage_authorship", "type" => "text_general", "indexed" => false, "stored" => true],
            ["name" => "accepted_name_usage_id", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "accepted_name_usage", "type" => "string_ci", "indexed" => true, "stored" => true],
            ["name" => "accepted_name_usage_authorship", "type" => "text_general", "indexed" => false, "stored" => true],
            ["name" => "accepted_name_usage_taxon_rank", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "original_name_usage_id", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "species_id", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "original_name_usage", "type" => "string_ci", "indexed" => true, "stored" => true],
            ["name" => "original_name_usage_authorship", "type" => "text_general", "indexed" => false, "stored" => true],
            ["name" => "nomenclatural_status", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "taxonomic_status", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "occurrence_status", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "establishment_means", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "threat_status", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "taxon_remarks", "type" => "text_general", "indexed" => true, "stored" => true],
            ["name" => "ibra_6_region", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "ibra_6_subregion", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "ibra_7_region", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "ibra_7_subregion", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "nrm_region", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "epbc", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "vrot", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "ffg", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "profile", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "media", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
            ["name" => "vernacular_name", "type" => "string_ci", "indexed" => true, "stored" => true],
            ["name" => "apni_match_type", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "apni_match_verification_status", "type" => "string", "indexed" => true, "stored" => true],
            ["name" => "project", "type" => "string", "indexed" => true, "stored" => true, "multiValued" => true],
        ];
    }
}
