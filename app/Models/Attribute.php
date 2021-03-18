<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $version
 * @property string $guid
 * @property string $name
 * @property string $uri
 * @property string $description
 * @property string $remarks
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property AttributeValue[] $attributeValues
 * @property TaxonAttribute[] $taxonAttributes
 */
class Attribute extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'created_at', 
            'updated_at', 'version', 'guid', 'name', 'uri', 'description', 
            'remarks'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonAttributes(): HasMany
    {
        return $this->hasMany(TaxonAttribute::class);
    }
}
