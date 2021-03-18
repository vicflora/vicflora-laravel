<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $parent_item_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $version
 * @property string $guid
 * @property string $name
 * @property string $text_before
 * @property string $text_after
 * @property string $full_name_separator
 * @property boolean $is_enforced
 * @property boolean $is_in_full_name
 * @property integer $rank_id
 * @property TaxonTreeDefItem $parentItem
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'parent_item_id', 
            'created_at', 'updated_at', 'version', 'guid', 'name', 
            'text_before', 'text_after', 'full_name_separator', 'is_enforced', 
            'is_in_full_name', 'rank_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentItem(): BelongsTo
    {
        return $this->belongsTo(TaxonTreeDefItem::class, 'parent_item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts(): HasMany
    {
        return $this->hasMany(TaxonConcept::class);
    }
}
