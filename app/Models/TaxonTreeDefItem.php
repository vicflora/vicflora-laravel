<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $parentItemId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $version
 * @property string $guid
 * @property string $name
 * @property string $textBefore
 * @property string $textAfter
 * @property string $fullNameSeparator
 * @property boolean $isEnforced
 * @property boolean $isInFullName
 * @property integer $rankId
 * @property TaxonTreeDefItem $taxonTreeDefItem
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property TaxonConcept[] $taxonConcepts
 */
class TaxonTreeDefItem extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'parent_item_id', 'created_at', 'updated_at', 'version', 'guid', 'name', 'text_before', 'text_after', 'full_name_separator', 'is_enforced', 'is_in_full_name', 'rank_id'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonTreeDefItem()
    {
        return $this->belongsTo('App\Models\TaxonTreeDefItem', 'parent_item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts()
    {
        return $this->hasMany('App\Models\TaxonConcept');
    }
}
