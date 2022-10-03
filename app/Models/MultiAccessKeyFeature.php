<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * MultiAccessKeyFeature model
 * 
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $guid
 * @property string $name
 * @property string $description
 * @property integer $parent_id
 * @property integer $type
 * @property bool $inc_best
 * @property integer $key_id
 * @property MultiAccessKey $key
 * @property MultiAccessKeyFeature $parent
 * @property MultiAccessKeyFeature[] $children
 * @property MultiAccessKeyState[] $states
 */
class MultiAccessKeyFeature extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matrix_keys.features';

    /**
     * Get the key the feature is in
     *
     * @return BelongsTo
     */
    public function key(): BelongsTo
    {
        return $this->belongsTo(MultiAccessKey::class, 'key_id', 'id');
    }

    /**
     * Get the feature's parent
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MultiAccessKeyFeature::class, 'parent_id', 'id');
    }

    /**
     * Get the feature's children
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(MultiAccessKeyFeature::class, 'parent_id', 'id')
            ->where('feature_type', '!=', 'unit');
    }

    /**
     * Get the feature's states
     *
     * @return HasMany
     */
    public function states(): HasMany
    {
        return $this->hasMany(MultiAccessKeyState::class, 'feature_id', 'id')
                ->orderBy('id');
    }

    /**
     * Get the feature's unit
     *
     * @return HasOne
     */
    public function unit(): HasOne
    {
        return $this->hasOne(MultiAccessKeyFeature::class, 'parent_id', 'id')
            ->where('feature_type', 'unit');
    }

}
