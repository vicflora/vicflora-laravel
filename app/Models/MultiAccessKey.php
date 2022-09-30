<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MultiAccessKey model
 * 
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $guid
 * @property string $title
 * @property string $description
 * @property string $location
 * @property integer $created_by_id
 * @property integer $updated_by_id
 * @property MultiAccessKeyFeature $feature
 */
class MultiAccessKey extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matrix_keys.keys';

    /**
     * Get the features in the key
     *
     * @return HasMany
     */
    public function features():HasMany
    {
        return $this->hasMany(MultiAccessKeyFeature::class, 'key_id', 'id');
    }
}
