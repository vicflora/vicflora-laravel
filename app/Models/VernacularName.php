<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $taxon_concept_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $version
 * @property string $guid
 * @property string $name
 * @property boolean $is_preferred
 * @property string $name_usage
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_concept_id', 
            'created_at', 'updated_at', 'version', 'guid', 'name', 
            'is_preferred', 'name_usage', 'remarks'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }

}
