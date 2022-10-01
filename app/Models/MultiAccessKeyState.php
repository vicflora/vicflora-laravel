<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MultiAccessKeyState model
 * 
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $guid
 * @property string $name
 * @property string $description
 * @property integer $feature_id
 * @property MultiAccessKeyFeature $feature
 */
class MultiAccessKeyState extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'matrix_keys.states';

    /**
     * Get the feature the state belongs to
     *
     * @return BelongsTo
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(MultiAccessKeyFeature::class, 'feature_id', 'id');
    }
}
