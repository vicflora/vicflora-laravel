<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $guid
 * @property string $name
 * @property string $country
 * @property string $state_territory
 * @property integer $sort_order
 * @property string $img
 * 
 * @property TaxonConceptFloraLink[] $taxonConceptFloraLinks
 */
class Flora extends Model
{
    protected $table = 'floras';

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
            'updated_at', 'guid', 'name', 'country', 'state_province', 
            'sort_order', 'img'];

    /**
     * @return HasMany
     */
    public function taxonConceptFloraLinks(): HasMany
    {
        return $this->hasMany(TaxonConceptFloraLink::class);
    }
    
}
