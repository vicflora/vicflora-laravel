<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
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
 * @property MultiAccessKeyCharacter $feature
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
     * Get the character the state belongs to
     *
     * @return BelongsTo
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(MultiAccessKeyCharacter::class, 'feature_id', 'id');
    }


    /**
     * Get images for the state
     *
     * @return Collection
     */
    public function getImagesAttribute(): Collection
    {
        return Image::select('images.*')
            ->join('matrix_keys.links', 'images.uid', '=', 'links.uid')
            ->join('matrix_keys.state_links', 'links.id', '=', 'state_links.link_id')
            ->where('state_links.state_id', $this->id)
            ->get();
    }
}
