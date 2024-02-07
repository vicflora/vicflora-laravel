<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConservationList extends Model
{
    /**
     * Get the threat statuses for the conservation list
     *
     * @return HasMany
     */
    public function threatStatuses(): HasMany
    {
        return $this->hasMany(ThreatStatus::class);
    }
}
