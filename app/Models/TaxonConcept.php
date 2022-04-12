<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Staudenmeir\LaravelCte\Eloquent\QueriesExpressions;


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
 * @property TaxonRelationship[] $subjectOfTaxonRelationships
 * @property TaxonRelationship[] $objectOfTaxonRelationships
 * @property Profile[] $profiles
 * @property Profile[] $profilesAccepted
 * @property Image[] $images
 * @property Image[] $imagesAccepted
 * @property TaxonBioregion[] $bioregions
 * @property TaxonLocalGovernmentArea[] $localGovernmentAreas
 * @property TaxonParkReserve[] $parkReserves
 * @property TaxonConceptReference[] $taxonConceptReferences
 * @property TaxonConceptFloraLink[] $taxonConceptFloraLinks
 */
class TaxonConcept extends BaseModel
{
    use QueriesExpressions;
    

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
            'according_to_id', 'taxon_tree_def_item_id', 'accepted_id', 
            'parent_id', 'taxonomic_status_id', 'occurrence_status_id', 
            'establishment_means_id', 'degree_of_establishment_id', 
            'created_at', 'updated_at', 'rank_id', 'remarks', 'editor_notes', 
            'version', 'guid'];

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
        return $this->belongsTo(EstablishmentMeans::class, 
                'establishment_means_id');
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
     * @return HasMany
     */
    public function taxonConceptReferences(): HasMany
    {
        return $this->hasMany(TaxonConceptReference::class);
    }

    /**
     * Taxon Concept References sorted by author and publication date
     *
     * @return EloquentCollection
     */
    public function getOrderedReferencesAttribute(): EloquentCollection
    {
        return $this->taxonConceptReferences()
                ->join('references', 'taxon_concept_references.reference_id', 
                        '=', 'references.id')
                ->join('agents', 'references.author_id', '=', 'agents.id')
                ->select('taxon_concept_references.*')
                ->orderBy('agents.name')
                ->orderBy('references.publication_year')
                ->get();
    }

    /**
     * @return HasMany
     */
    public function taxonConceptFloraLinks(): HasMany
    {
        return $this->hasMany(TaxonConceptFloraLink::class);
    }


    public function getOrderedFloraLinksAttribute(): EloquentCollection
    {
        return $this->taxonConceptFloraLinks()
                ->join('floras', 'taxon_concept_flora_links.flora_id', '=', 
                        'floras.id')
                ->orderBy('floras.sort_order')
                ->get();
    }


    /**
     * @return BelongsToMany
     */
    public function matrixKeys(): BelongsToMany
    {
        return $this->belongsToMany(MatrixKey::class);
    }

    /**
     * Get taxonRank
     *
     * @return string|null
     */
    public function getTaxonRankAttribute(): ?string
    {
        if ($this->taxon_tree_def_item_id) {
            $tr = TaxonTreeDefItem::find($this->taxon_tree_def_item_id);
            return $tr->name;
        }
        return null;
    }

    /**
     * Set taxon_tree_def_item_id if taxonRank attribute is provided
     *
     * @param string|null $value
     * @return void
     */
    public function setTaxonRankAttributeItem(?string $value)
    {
        if ($value) {
            $tr = TaxonTreeDefItem::where('name', $value)->first();
            $this->taxon_tree_def_item_id = $tr->id;
        }
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjectOfTaxonRelationships(): HasMany
    {
        return $this->hasMany(TaxonRelationship::class, 
                'object_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function objectOfTaxonRelationships(): HasMany
    {
        return $this->hasMany(TaxonRelationship::class, 
                'subject_taxon_concept_id');
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
        if ($this->taxonomicStatus->name == 'accepted') {
            return Profile::where('accepted_id', $this->id)
                    ->where('is_current', true)->first();
        }
        return null;
    }

    /**
     * @return void
     */
    public function getParentAttribute()
    {
        if ($this->taxonomicStatus->name == 'accepted') {
            return TaxonConcept::where('id', $this->parent_id)->first();
        }
        return null;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getChildrenAttribute(): Collection
    {
        if ($this->taxonomicStatus->name === 'accepted') {
            return TaxonConcept::select('taxon_concepts.*', 'taxon_names.full_name')
                ->join('taxonomic_statuses', 
                        'taxon_concepts.taxonomic_status_id', '=', 
                        'taxonomic_statuses.id')
                ->join('taxon_names', 'taxon_concepts.taxon_name_id', '=', 
                        'taxon_names.id')
                ->where('taxon_concepts.parent_id', $this->id)
                ->where('taxonomic_statuses.name', 'accepted')
                ->orderBy('full_name')
                ->get();
        }
        return collect([]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getSiblingsAttribute(): Collection
    {
        if ($this->taxonomicStatus->name === 'accepted') {
            return TaxonConcept::select('taxon_concepts.*', 
                    'taxon_names.full_name')
                ->join('taxonomic_statuses', 
                        'taxon_concepts.taxonomic_status_id', '=', 
                        'taxonomic_statuses.id')
                ->join('taxon_names', 'taxon_concepts.taxon_name_id', '=', 
                        'taxon_names.id')
                ->where('taxon_concepts.parent_id', $this->parent_id)
                ->where('taxonomic_statuses.name', 'accepted')
                ->orderBy('full_name')
                ->get();
        }
        return collect([]);
    }

    /**
     * Gets ancestors by recursive query
     *
     * @return EloquentCollection
     */
    public function getAncestorsAttribute(): EloquentCollection
    {
        $query = TaxonConcept::where('id', $this->parent_id)
        ->union(
            TaxonConcept::select('taxon_concepts.*')
                ->join('ancestors', 'ancestors.parent_id', '=', 
                        'taxon_concepts.id')
        );

        return TaxonConcept::from('ancestors')
                ->withRecursiveExpression('ancestors', $query)
                ->get();
    }

    /**
     * Gets descendants by recursive query
     *
     * @return EloquentCollection
     */
    public function getDescendantsAttribute(): EloquentCollection
    {
        $query = TaxonConcept::where('parent_id', $this->id)
        ->union(
            TaxonConcept::select('taxon_concepts.*')
                ->join('descendants', 'descendants.id', '=', 
                        'taxon_concepts.parent_id')
        );

        return TaxonConcept::from('descendants')
                ->withRecursiveExpression('descendants', $query)
                ->get();
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
     * @return \Illuminate\Support\Collection
     */
    public function getSynonymsAttribute(): Collection
    {
        $usages = TaxonConcept::where('accepted_id', $this->id)
                ->whereHas('taxonomicStatus', function(Builder $query) {
                    $query->whereIn('name', ['synonym', 'heterotypicSynonym', 
                            'homotypicSynonym']);
                })
                ->get();

        $synonyms = [];
        foreach ($usages as $usage) {
            $synonyms[] = $usage->taxonName;
        }
        return collect($synonyms);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSynonymUsagesAttribute(): EloquentCollection
    {
        return TaxonConcept::where('accepted_id', $this->id)
                ->whereHas('taxonomicStatus', function(Builder $query) {
                    $query->whereIn('name', ['synonym', 'heterotypicSynonym', 
                            'homotypicSynonym']);
                })
                ->get();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMisapplicationsAttribute(): EloquentCollection
    {
        return TaxonConcept::where('accepted_id', $this->id)
                ->whereHas('taxonomicStatus', function(Builder $query) {
                    $query->whereIn('name', ['misapplication', 'misapplied']);
                })
                ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bioregions(): HasMany
    {
        return $this->hasMany(TaxonBioregion::class, 'taxon_concept_id', 'guid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function localGovernmentAreas(): HasMany
    {
        return $this->hasMany(TaxonLocalGovernmentArea::class,
                'taxon_concept_id', 'guid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parkReserves(): HasMany
    {
        return $this->hasMany(TaxonParkReserve::class, 'taxon_concept_id',
                'guid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function occurrences(): HasMany
    {
        return $this->hasMany(TaxonOccurrence::class, 'taxon_concept_id',
                'guid');
    }

    /**
     * @return string|null
     */
    public function getTaxonomicStatusNameAttribute(): ?string
    {
        if ($this->taxonomic_status_id) {
            $ts = TaxonomicStatus::find($this->taxonomic_status_id);
            return $ts ? $ts->name : null;
        }
        return null;
    }

    /**
     * Sets taxonomic_status_id, if taxonomicStatusName attribute is supplied
     *
     * @param string|null $value
     * @return void
     */
    public function setTaxonomicStatusNameAttribute($value)
    {
        if ($value) {
            $ts = TaxonomicStatus::where('name', $value)->first();
            $this->taxonomic_status_id = $ts->id;
        }
    }

    /**
     * @return string|null
     */
    public function getOccurrenceStatusNameAttribute(): ?string
    {
        if ($this->occurrence_status_id) {
            $os = OccurrenceStatus::find($this->occurrence_status_id);
            return $os ? $os->name : null;
        }
        return null;
    }

    /**
     * Sets taxonomic_status_id, if taxonomicStatusName attribute is supplied
     *
     * @param string|null $value
     * @return void
     */
    public function setOccurrenceStatusNameAttribute($value)
    {
        if ($value) {
            $os = OccurrenceStatus::where('name', $value)->first();
            $this->occurrence_status_id = $os->id;
        }
    }

    /**
     * @return string|null
     */
    public function getEstablishmentMeansNameAttribute(): ?string
    {
        if ($this->establishment_means_id) {
            $em = EstablishmentMeans::find($this->establishment_means_id);
            return $em ? $em->name : null;
        }
        return null;
    }

    /**
     * Sets taxonomic_status_id, if taxonomicStatusName attribute is supplied
     *
     * @param string|null $value
     * @return void
     */
    public function setEstablishmentMeansNameAttribute($value)
    {
        if ($value) {
            $em = EstablishmentMeans::where('name', $value)->first();
            $this->establishment_means_id = $em->id;
        }
    }

    /**
     * @return string|null
     */
    public function getDegreeOfEstablishmentNameAttribute(): ?string
    {
        if ($this->establishment_means_id) {
            $doe = DegreeOfEstablishment::find($this->degree_of_establishment_id);
            return $doe ? $doe->name : null;
        }
        return null;
    }

    /**
     * Sets taxonomic_status_id, if taxonomicStatusName attribute is supplied
     *
     * @param string|null $value
     * @return void
     */
    public function setDegreeOfEstablishmentNameAttribute($value)
    {
        if ($value) {
            $doe = DegreeOfEstablishment::where('name', $value)->first();
            $this->degree_of_establishment_id = $doe->id;
        }
    }

    /**
     * Check if model has associated images
     *
     * @return boolean
     */
    public function getHasImagesAttribute(): bool
    {
        return $this->imagesAccepted()->exists();
    }


    /**
     * Check if model has associated specimen images
     *
     * @return boolean
     */
    public function getHasSpecimenImagesAttribute(): bool
    {
        return $this->specimenImagesAccepted()->exists();
    }


    /**
     * EPBC
     *
     * @return string|null
     */
    public function getEpbcAttribute(): ?string
    {
        $vba = VbaTaxon::where('taxon_name_id', $this->taxonName->guid)->first();
        if ($vba) {
            return $vba->epbc;
        }
        return null;
    }

    /**
     * FFG
     *
     * @return string|null
     */
    public function getFfgAttribute(): ?string
    {
        $vba = VbaTaxon::where('taxon_name_id', $this->taxonName->guid)->first();
        if ($vba) {
            return $vba->ffg;
        }
        return null;
    }

    /**
     * Vic. Advisory List
     *
     * @return string|null
     */
    public function getVicAdvisoryAttribute(): ?string
    {
        $vba = VbaTaxon::where('taxon_name_id', $this->taxonName->guid)->first();
        if ($vba) {
            return $vba->vic_adv;
        }
        return null;
    }

    /**
     * @return HasMany
     */
    public function changes(): HasMany
    {
        return $this->hasMany(Change::class, 'from_id');
    }
}
