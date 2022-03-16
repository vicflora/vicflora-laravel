<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MatrixKey extends Model
{
    /**
     * @return BelongsToMany
     */
    public function taxonConcepts(): BelongsToMany
    {
        return $this->belongsToMany(TaxonConcept::class);
    }
}
