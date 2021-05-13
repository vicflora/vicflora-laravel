<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $term_id
 * @property integer $glossary_id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $name
 * @property string $guid
 * @property integer $version
 * @property string $timestamp_created
 * @property string $timestamp_modified
 * @property GlossaryTerm $term
 */
class GlossaryCategory extends Model
{
    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'glossary';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('vicflora', function (Builder $builder) {
            $builder->where('glossary_id', 4);
        });
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(GlossaryTerm::class);
    }   

}
