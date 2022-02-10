<?php

namespace App\Models;

use App\Models\BaseModel;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'created_by_id');
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'updated_by_id');
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

    /**
     * Date the first version was created
     *
     * @return string|null
     */
    public function getCreatedAttribute(): ?string
    {
        if (!$this->source_id) {
            $origProfile = Profile::where('guid', $this->guid)
                    ->where('version', 1)
                    ->first();
            $date = DateTime::createFromFormat('Y-m-d H:i:s',
                    $origProfile->created_at);
            return $date->setTimezone(new DateTimeZone('Australia/Melbourne'))
                    ->format('Y-m-d');
        }
        return null;
    }

    /**
     * Creator of the first version
     *
     * @return Agent|null
     */
    public function getCreatorAttribute(): ?Agent
    {
        if ($this->is_current && !$this->source_id) {
            $origProfile = Profile::where('guid', $this->guid)
                    ->where('version', 1)
                    ->first();
            return $origProfile->createdBy;
        }
        return null;
    }

    /**
     * Date the updated version was created
     *
     * @return string|null
     */
    public function getModifiedAttribute(): ?string
    {
        if ($this->is_updated) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s',
                    $this->updated_at);
            return $date->setTimezone(new DateTimeZone('Australia/Melbourne'))
                    ->format('Y-m-d');
        }
        return null;
    }

    /**
     * Creator of the updated version
     *
     * @return Agent|null
     */
    public function getUpdatedByAttribute(): ?Agent
    {
        if ($this->is_updated) {
            return $this->createdBy;
        }
        return null;
    }
}
