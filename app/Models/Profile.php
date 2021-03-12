<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $taxonConceptId
 * @property integer $acceptedId
 * @property integer $sourceId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $version
 * @property string $guid
 * @property integer $taxonomicStatusId
 * @property string $profile
 * @property boolean $isCurrent
 * @property boolean $isUpdated
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property Reference $reference
 * @property TaxonConcept $taxonConcept
 * @property TaxonConcept $acceptedConcept
 */
class Profile extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_concept_id', 'accepted_id', 'source_id', 'created_at', 'updated_at', 'version', 'guid', 'taxonomic_status_id', 'profile', 'is_current', 'is_updated'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference()
    {
        return $this->belongsTo('App\Models\Reference', 'source_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept');
    }
}
