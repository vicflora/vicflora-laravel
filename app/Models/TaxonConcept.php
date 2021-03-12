<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $taxonNameId
 * @property integer $accordingToId
 * @property integer $taxonTreeDefItemId
 * @property integer $acceptedId
 * @property integer $parentId
 * @property integer $taxonomicStatusId
 * @property integer $occurrenceStatusId
 * @property integer $establishmentMeansId
 * @property integer $degreeOfEstablishmentId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $rankId
 * @property string $remarks
 * @property string $editorNotes
 * @property integer $version
 * @property string $guid
 * @property DegreeOfEstablishment $degreeOfEstablishment
 * @property EstablishmentMean $establishmentMean
 * @property OccurrenceStatus $occurrenceStatus
 * @property TaxonomicStatus $taxonomicStatus
 * @property TaxonTreeDefItem $taxonTreeDefItem
 * @property TaxonConcept $taxonConcept
 * @property TaxonConcept $taxonConcept
 * @property Reference $reference
 * @property TaxonName $taxonName
 * @property Agent $agent
 * @property Agent $agent
 * @property TaxonTreeItem[] $taxonTreeItems
 * @property TaxonRelationship[] $taxonRelationships
 * @property TaxonRelationship[] $taxonRelationships
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_name_id', 'according_to_id', 'taxon_tree_def_item_id', 'accepted_id', 'parent_id', 'taxonomic_status_id', 'occurrence_status_id', 'establishment_means_id', 'degree_of_establishment_id', 'created_at', 'updated_at', 'rank_id', 'remarks', 'editor_notes', 'version', 'guid'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function degreeOfEstablishment()
    {
        return $this->belongsTo('App\Models\DegreeOfEstablishment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function establishmentMean()
    {
        return $this->belongsTo('App\Models\EstablishmentMean', 'establishment_means_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function occurrenceStatus()
    {
        return $this->belongsTo('App\Models\OccurrenceStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonomicStatus()
    {
        return $this->belongsTo('App\Models\TaxonomicStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonTreeDefItem()
    {
        return $this->belongsTo('App\Models\TaxonTreeDefItem');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference()
    {
        return $this->belongsTo('App\Models\Reference', 'according_to_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonName()
    {
        return $this->belongsTo('App\Models\TaxonName');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonTreeItems()
    {
        return $this->hasMany('App\Models\TaxonTreeItem');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjectOfTaxonRelationships()
    {
        return $this->hasMany('App\Models\TaxonRelationship', 'object_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function objectOfTaxonRelationships()
    {
        return $this->hasMany('App\Models\TaxonRelationship', 'subject_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profilesAccepted()
    {
        return $this->hasMany('App\Models\Profile', 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany('App\Models\Profile');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imagesAccepted()
    {
        return $this->hasMany('App\Models\Image', 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'taxon_id');
    }
}
