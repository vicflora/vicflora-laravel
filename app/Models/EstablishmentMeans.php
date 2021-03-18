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
 * @property string $name
 * @property string $uri
 * @property string $label
 * @property string $description
 * @property string $guid
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property TaxonConcept[] $taxonConcepts
 */
class EstablishmentMeans extends BaseModel
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
            'updated_at', 'name', 'uri', 'label', 'description', 'guid'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts(): HasMany
    {
        return $this->hasMany(TaxonConcept::class, 'establishment_means_id');
    }
}
