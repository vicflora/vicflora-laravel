<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $taxonConceptId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $version
 * @property string $guid
 * @property string $name
 * @property boolean $isPreferred
 * @property string $nameUsage
 * @property string $remarks
 * @property TaxonConcept $taxonConcept
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 */
class VernacularName extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_concept_id', 'created_at', 'updated_at', 'version', 'guid', 'name', 'is_preferred', 'name_usage', 'remarks'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept');
    }

}
