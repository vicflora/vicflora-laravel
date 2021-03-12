<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $taxonConceptId
 * @property integer $attributeId
 * @property integer $attributeValueId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $version
 * @property string $remarks
 * @property TaxonConcept $taxonConcept
 * @property Attribute $attribute
 * @property AttributeValue $attributeValue
 * @property Agent $createdByID
 * @property Agent $modifiedByID
 */
class TaxonAttribute extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_concept_id', 'attribute_id', 'attribute_value_id', 'created_at', 'updated_at', 'version', 'remarks'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attributeValue()
    {
        return $this->belongsTo('App\Models\AttributeValue');
    }
}
