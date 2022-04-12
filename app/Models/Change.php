<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Change extends BaseModel
{
    protected $fillable = [
        'id',
        'guid',
        'created_at',
        'updated_at',
        'from_id',
        'to_id',
        'change_type_id',
        'change_source_id',
        'created_by_id',
    ];

    /**
     * @return BelongsTo
     */
    public function from(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'from_id');
    }

    /**
     * @return BelongsTo
     */
    public function to(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'to_id');
    }

    /**
     * @return BelongsTo
     */
    public function changeType(): BelongsTo
    {
        return $this->belongsTo(TaxonomicStatus::class, 'change_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function changeSource(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'change_source_id');
    }

    /**
     * @return string|null
     */
    public function getChangeTypeNameAttribute(): ?string
    {
        if ($this->change_type_id) {
            $ts = TaxonomicStatus::find($this->change_type_id);
            return $ts ? $ts->name : null;
        }
        return null;
    }

    /**
     * Sets taxonomic_status_id, if taxonomicStatusName attribute is supplied
     *
     * @param string|null $value
     * @return void
     */
    public function setChangeTypeNameAttribute($value)
    {
        if ($value) {
            $ts = TaxonomicStatus::where('name', $value)->first();
            $this->change_type_id = $ts->id;
        }
    }

}
