<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IucnCategory extends Model
{
    /**
     * Get the threat statuses for the IUCN category
     *
     * @return HasMany
     */
    public function threatStatuses(): HasMany
    {
        return $this->hasMany(ThreatStatus::class);
    }
}
