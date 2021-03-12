<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property string $createdAt
 * @property string $updatedAt
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'created_at', 'updated_at', 'version', 'guid', 'name', 'uri', 'description', 'remarks'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributeValues()
    {
        return $this->hasMany('App\Models\AttributeValue');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonAttributes()
    {
        return $this->hasMany('App\Models\TaxonAttribute');
    }
}
