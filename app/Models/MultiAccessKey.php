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
     * Get the characters in the key. These are features that have no children.
     *
     * @return HasMany
     */
    public function characters(): HasMany
    {
        return $this->hasMany(MultiAccessKeyCharacter::class, 'key_id', 'id')
            ->whereNotIn('feature_type', ['group', 'unit'])
            ->orderBy('id');
    }

    /**
     * Get character groups. These are the features that have children.
     *
     * @return HasMany
     */
    public function characterGroups(): HasMany
    {
        return $this->hasMany(MultiAccessKeyCharacter::class, 'key_id', 'id')
            ->where('feature_type', 'group')
            ->orderBy('id');
    }
}
