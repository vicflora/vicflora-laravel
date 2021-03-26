<?php

namespace App\Models;

use App\Models\BaseModel;
use GraphQL\Type\Definition\ResolveInfo;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $taxon_name_id
 * @property integer $according_to_id
 * @property integer $taxon_tree_def_item_id
 * @property integer $accepted_id
 * @property integer $parent_id
 * @property integer $taxonomic_status_id
 * @property integer $occurrence_status_id
 * @property integer $establishment_means_id
 * @property integer $degree_of_establishment_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $rank_id
 * @property string $remarks
 * @property string $editor_notes
 * @property integer $version
 * @property string $guid
 * @property DegreeOfEstablishment $degreeOfEstablishment
 * @property EstablishmentMean $establishmentMean
 * @property OccurrenceStatus $occurrenceStatus
 * @property TaxonomicStatus $taxonomicStatus
 * @property TaxonTreeDefItem $taxonTreeDefItem
 * @property TaxonConcept $acceptedConcept
 * @property TaxonConcept $parent
 * @property Reference $accordingTo
 * @property TaxonName $taxonName
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property TaxonTreeItem[] $taxonTreeItem
 * @property TaxonRelationship[] $subjectOfTaxonRelationships
 * @property TaxonRelationship[] $objectOfTaxonRelationships
 * @property Profile[] $profiles
 * @property Profile[] $profilesAccepted
 * @property Image[] $images
 * @property Image[] $imagesAccepted
 */
class TaxonConcept extends BaseModel
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_name_id', 
            'according_to_id', 'taxon_tree_def_item_id', 'accepted_id', 'parent_id', 
            'taxonomic_status_id', 'occurrence_status_id', 'establishment_means_id', 
            'degree_of_establishment_id', 'created_at', 'updated_at', 'rank_id', 
            'remarks', 'editor_notes', 'version', 'guid'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function degreeOfEstablishment(): BelongsTo
    {
        return $this->belongsTo(DegreeOfEstablishment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function establishmentMeans(): BelongsTo
    {
        return $this->belongsTo(EstablishmentMeans::class, 'establishment_means_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function occurrenceStatus(): BelongsTo
    {
        return $this->belongsTo(OccurrenceStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonomicStatus(): BelongsTo
    {
        return $this->belongsTo(TaxonomicStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonTreeDefItem(): BelongsTo
    {
        return $this->belongsTo(TaxonTreeDefItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accordingTo(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'according_to_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonTreeItem(): HasOne
    {
        return $this->hasOne(TaxonTreeItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjectOfTaxonRelationships(): HasMany
    {
        return $this->hasMany(TaxonRelationship::class, 'object_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function objectOfTaxonRelationships(): HasMany
    {
        return $this->hasMany(TaxonRelationship::class, 'subject_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profilesAccepted(): HasMany
    {
        return $this->hasMany(Profile::class, 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imagesAccepted(): HasMany
    {
        return $this->hasMany(Image::class, 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'taxon_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specimenImagesAccepted(): HasMany
    {
        return $this->hasMany(SpecimenImage::class, 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specimenImages(): HasMany
    {
        return $this->hasMany(SpecimenImage::class, 'taxon_concept_id');
    }

    /**
     * @return \App\Models\Profile|null
     */
    public function getCurrentProfileAttribute()
    {
        return Profile::where('taxon_concept_id', $this->id)
                ->where('is_current', true)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChildrenAttribute(): Collection
    {
        return TaxonConcept::where('parent_id', $this->id)
                ->whereHas('taxonomicStatus', function (Builder $query) {
                    $query->where('name', 'accepted');
                })
                ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSiblingsAttribute(): Collection
    {
        return TaxonConcept::where('parent_id', $this->parent_id)
                ->whereHas('taxonomicStatus', function (Builder $query) {
                    $query->where('name', 'accepted');
                })
                ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHigherClassificationAttribute(): Collection
    {
        $node = TaxonTreeItem::where('taxon_concept_id', $this->id)
                ->first();
        
        return TaxonTreeItem::where('node_number', '<', $node->node_number)
                ->where('highest_descendant_node_number', '>=', 
                        $node->node_number)
                ->get();
    }

    /**
     * @return \App\Models\Image|null
     */
    public function getHeroImageAttribute()
    {
        $node = TaxonTreeItem::where('taxon_concept_id', $this->id)->first();

        if ($node) {
            return Image::whereHas('taxonConcept', function (Builder $query) use ($node) {
                $query->whereHas('taxonomicStatus', function(Builder $query) {
                            $query->where('name', 'accepted');
                        })
                        ->whereHas('taxonTreeItem', function (Builder $query) use ($node) {
                            $query->where('node_number', '>=', $node->node_number)
                                    ->where('node_number', '<=', $node->highest_descendant_node_number);
                        });
                    })
                    ->where('pixel_x_dimension', '>', 0)
                    ->orderBy('hero_image', 'desc')
                    ->orderBy('subtype', 'desc')
                    ->orderBy('rating', 'desc')
                    ->orderBy(DB::raw('random()'))
                    ->first();
        }
    }

    /**
     * @return \App\Models\VernacularName|null
     */
    public function getPreferredVernacularNameAttribute()
    {
        return VernacularName::where('taxon_concept_id', $this->id)
                ->where('is_preferred', true)->first();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBioregionsAttribute()
    {
        return TaxonBioregion::where('taxon_guid', $this->guid)->get();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOccurrencesAttribute()
    {
        $key = $this->rank_id > 220 ? 'accepted_name_usage_id' : 'species_id';
        return Occurrence::where('accepted_name_usage_id', $this->guid)->get();
    }


    /**
     * Custom builder for occurrences paginator
     *
     * @param TaxonConcept $taxonConcept
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function occurrences($taxonConcept): Builder
    {
        $key = $taxonConcept->rank_id > 220 ? 'accepted_name_usage_id' : 'species_id';
        return Occurrence::where($key, $taxonConcept->guid);
    }

    public function getMapLinksAttribute()
    {
        $maps = [];
        $key = $this->rank_id > 220 ? 'accepted_name_usage_id' : 'species_id';
        $bioregionLayer = $this->rank_id > 220 ? 'distribution_bioregion_view' : 'distribution_bioregion_species_view';
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
                    "{$key}='{$this->guid}' " . 
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
                    "{$key}='{$this->guid}' " . 
                    "AND occurrence_status NOT IN ('doubtful', 'absent', 'excluded')"
        ];

        $maps['distributionMap'] = $url . '?' . http_build_query($queryVars);

        $name = TaxonName::find($this->taxon_name_id);
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

    public function getIdentificationKeysAttribute()
    {
        $client = new Client(['base_uri' => 'https://data.rbg.vic.gov.au']);
        $res = $client->request('GET', '/keybase-ws/ws/search_items/'. $this->taxonName->full_name, [
            'query' => [
                'project' => 10,
            ]
        ]);
        
        return collect(json_decode($res->getBody()) ?: []);
    }

    /**
     * Gets images for this taxon and its members
     *
     * @param TaxonConcept $taxonConcept
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function imagesPaginator($taxonConcept): Builder
    {
        $node = TaxonTreeItem::where('taxon_concept_id', $taxonConcept->id)->first();
        return Image::whereHas('acceptedConcept', function(Builder $query) use ($node) {
                $query->whereHas('taxonTreeItem', function(Builder $query) use ($node) {
                        $query->where('node_number', '>=', $node->node_number)
                                ->where('node_number', '<=', $node->highest_descendant_node_number);
                });
        });

    }

    /**
     * Gets specimen images for this taxon and its members
     *
     * @param TaxonConcept $taxonConcept
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function specimenImagesPaginator($taxonConcept): Builder
    {
        $node = TaxonTreeItem::where('taxon_concept_id', $taxonConcept->id)->first();
        return SpecimenImage::whereHas('acceptedConcept', function(Builder $query) use ($node) {
                $query->whereHas('taxonTreeItem', function(Builder $query) use ($node) {
                        $query->where('node_number', '>=', $node->node_number)
                                ->where('node_number', '<=', $node->highest_descendant_node_number);
                });
        });
    }

}
