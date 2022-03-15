<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Type\Integer;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $guid
 * @property integer $taxon_concept_id
 * @property integer $flora_id
 * @property string $url
 * 
 * @property TaxonConcept $taxonConcept
 * @property Flora $flora
 */
class TaxonConceptFloraLink extends Model
{

    protected $table = 'taxon_concept_flora_links';

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
            'updated_at', 'guid', 'taxon_concept_id', 'flora_id', 'url', 
            'remarks'];

    /**
     * @return BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }

    /**
     * @return BelongsTo
     */
    public function flora(): BelongsTo
    {
        return $this->belongsTo(Flora::class);
    }

    /**
     * @return string
     */
    public function getFloraNameAttribute(): string
    {
        return $this->flora->name;
    }

    /**
     * @return string
     */
    public function getIconAttribute(): string
    {
        return $this->flora->img;
    }

    /**
     * @return integer
     */
    public function getSortOrderAttribute(): int
    {
        return $this->flora->sort_order;
    }
}
