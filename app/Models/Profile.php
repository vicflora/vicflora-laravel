<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $taxon_concept_id
 * @property integer $accepted_id
 * @property integer $source_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $version
 * @property string $guid
 * @property integer $taxonomic_status_id
 * @property string $profile
 * @property boolean $is_current
 * @property boolean $is_updated
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property Reference $source
 * @property TaxonConcept $taxonConcept
 * @property TaxonConcept $acceptedConcept
 */
class Profile extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_concept_id', 'accepted_id', 'source_id', 'created_at', 'updated_at', 'version', 'guid', 'taxonomic_status_id', 'profile', 'is_current', 'is_updated'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo('App\Models\Reference', 'source_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept');
    }

    /**
     * Concatenates the version with the guid to get a unique ID
     *
     * @return void
     */
    public function getUniqIdAttribute()
    {
        return $this->guid . ":" . $this->version;
    }

    /**
     * Get taxonomicStatus controlled term
     *
     * @return string|null
     */
    public function getTaxonomicStatusNameAttribute(): ?string
    {
        if ($this->taxonomic_status_id) {
            $ts = TaxonomicStatus::find($this->taxonomic_status_id);
            return $ts->name;
        }
        return null;
    }

    /**
     * Set taxonomic_status_id if taxonomicStatusName attribute is provided
     *
     * @param string|null $value
     * @return void
     */
    public function setTaxonomicStatusNameAttribute(?string $value)
    {
        if ($value) {
            $ts = TaxonomicStatus::where('name', $value)->first();
            $this->taxonomic_status_id = $ts->id;
        }
    }
}
