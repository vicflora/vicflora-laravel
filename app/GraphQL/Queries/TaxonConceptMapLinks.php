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
        if ($taxonConcept->taxonomicStatus->name != 'accepted') {
            return null;
        }
        $maps = [];
        $url = 'https://data.rbg.vic.gov.au/geoserver/vicflora-mapper/wms';
        $queryVars = [ 
            'service' => 'WMS', 
            'version' => '1.1.0', 
            'request' => 'GetMap', 
            'layers' => 'vicflora-mapper:victoria_outline,vicflora-mapper:taxon_occurrences', 
            'styles' => 'polygon-no-fill-black-outline,', 
            'bbox' => '140.8,-39.3,150.2,-33.8', 
            'width' => '600', 
            'height' => '363', 
            'srs' => 'EPSG:4326', 
            'format' => 'image/svg', 
            'cql_filter' => "INCLUDE;" . 
                    "taxon_concept_id='{$taxonConcept->guid}' " . 
                    "AND establishment_means NOT IN ('cultivated') " .
                    "AND occurrence_status NOT IN ('doubtful','absent', 'excluded')"            
        ];

        $maps['profileMap'] = $url . '?' . http_build_query($queryVars);

        $name = TaxonName::find($taxonConcept->taxon_name_id);
        $nameSlug = urlencode($name->full_name);
        $year = date('Y');
        $source = <<<EOT
            AVH ({$year}). <i>Australia's Virtual Herbarium</i>, Council of Heads of 
            Australasian Herbaria, &lt;<a href="https://avh.chah.org.au">http://avh.chah.org.au</a>&gt;.
            <a href="https://avh.ala.org.au/occurrences/search?taxa={$nameSlug}" target="_blank">Find {$name->full_name} in AVH <i class="fa fa-external-link"></i></a>;
            <i>Victorian Biodiversity Atlas</i>, © The State of Victoria, Department of Environment and Primary Industries (published Dec. 2014)
            <a href="https://biocache.ala.org.au/occurrences/search?taxa={$nameSlug}&fq=data_resource_uid:dr1097" target="_blank">Find {$name->full_name} in Victorian Biodiversity Atlas <i class="fa fa-external-link"></i></a>
EOT;
        $maps['mapSource'] = trim(preg_replace('/\s+/', ' ', $source));
        
        return $maps;
    }
}
