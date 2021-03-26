<?php

namespace App\GraphQL\Queries;

use App\Models\TaxonConcept;
use App\Models\TaxonName;

class TaxonConceptMapLinks
{
    /**
     * @param  \App\Models\TaxonConcept  $taxonConcept
     */
    public function __invoke(TaxonConcept $taxonConcept)
    {
        $maps = [];
        $key = $taxonConcept->rank_id > 220 ? 'accepted_name_usage_id' : 'species_id';
        $bioregionLayer = $taxonConcept->rank_id > 220 ? 'distribution_bioregion_view' : 'distribution_bioregion_species_view';
        $url = 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms';
        $queryVars = [ 
            'service' => 'WMS', 
            'version' => '1.1.0', 
            'request' => 'GetMap', 
            'layers' => 'vicflora:cst_vic,vicflora:occurrence_view', 
            'styles' => 'polygon_no-fill_black-outline,', 
            'bbox' => '140.8,-39.3,150.2,-33.8', 
            'width' => '600', 
            'height' => '363', 
            'srs' => 'EPSG:4326', 
            'format' => 'image/svg', 
            'cql_filter' => "FEAT_CODE IN ('mainland','island');" . 
                    "{$key}='{$taxonConcept->guid}' " . 
                    "AND establishment_means NOT IN ('cultivated') " .
                    "AND occurrence_status NOT IN ('doubtful','absent', 'excluded')"            
        ];

        $maps['profileMap'] = $url . '?' . http_build_query($queryVars);

        $queryVars = [
            'service' => 'WMS', 
            'version' => '1.1.0', 
            'request' => 'GetMap', 
            'layers' => "vicflora:cst_vic,vicflora:{$bioregionLayer},vicflora:vicflora_bioregion,vicflora:cst_vic,vicflora:occurrence_view", 
            'styles' => ',polygon_establishment_means,polygon_no-fill_grey-outline,polygon_no-fill_black-outline,', 
            'bbox' => '140.8,-39.3,150.2,-33.8', 
            'width' => '480', 
            'height' => '291', 
            'srs' => 'EPSG:4326', 
            'format' => 'image/svg', 
            'cql_filter' => "FEAT_CODE IN ('mainland','island');" . 
                    "taxon_id='0c8e21a6-fe09-4835-84e1-d9531ad24728' " .
                    "AND occurrence_status NOT IN ('doubtful', 'absent');" . 
                    "INCLUDE;FEAT_CODE IN ('mainland','island');" . 
                    "{$key}='{$taxonConcept->guid}' " . 
                    "AND occurrence_status NOT IN ('doubtful', 'absent', 'excluded')"
        ];

        $maps['distributionMap'] = $url . '?' . http_build_query($queryVars);

        $name = TaxonName::find($taxonConcept->taxon_name_id);
        $nameSlug = urlencode($name->full_name);
        $source = <<<EOT
            AVH (2014). <i>Australia's Virtual Herbarium</i>, Council of Heads of 
            Australasian Herbaria, &lt;<a href="http://avh.chah.org.au">http://avh.chah.org.au</a>&gt;.
            <a href="https://avh.ala.org.au/occurrences/search?taxa={$nameSlug}" target="_blank">Find Aciphylla glacialis in AVH <i class="fa fa-external-link"></i></a>;
            <i>Victorian Biodiversity Atlas</i>, © The State of Victoria, Department of Environment and Primary Industries (published Dec. 2014)
            <a href="https://biocache.ala.org.au/occurrences/search?taxa={$nameSlug}&fq=data_resource_uid:dr1097" target="_blank">Find Aciphylla glacialis in Victorian Biodiversity Atlas <i class="fa fa-external-link"></i></a>
EOT;
        $maps['mapSource'] = trim(preg_replace('/\s+/', ' ', $source));
        
        return $maps;
    }
}
