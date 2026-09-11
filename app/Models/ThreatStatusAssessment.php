<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreatStatusAssessment extends Model
{
    protected $table = 'taxon_concept_threat_statuses';

    public function authority(): BelongsTo
    {
        return $this->belongsTo(ConservationList::class, 'conservation_list_id');
    }

    public function iucnCategory(): BelongsTo
    {
        return $this->belongsTo(IucnCategory::class);
    }
}
