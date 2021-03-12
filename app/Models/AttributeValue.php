<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $attributeId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $version
 * @property string $guid
 * @property string $value
 * @property string $uri
 * @property string $description
 * @property Attribute $attribute
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property TaxonAttribute[] $taxonAttributes
 */
class AttributeValue extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'attribute_id', 'created_at', 'updated_at', 'version', 'guid', 'value', 'uri', 'description'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonAttributes()
    {
        return $this->hasMany('App\Models\TaxonAttribute');
    }
}
