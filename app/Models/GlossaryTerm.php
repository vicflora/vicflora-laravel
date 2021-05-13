<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property integer $id
 * @property integer $glossary_id
 * @property integer $category_id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $name
 * @property string $definition
 * @property boolean $is_discouraged
 * @property string $local_id
 * @property string $language
 * @property string $name_addendum
 * @property string $guid
 * @property integer $version
 * @property string $timestamp_created
 * @property string $timestamp_modified
 * 
 * @property GlossaryCategory $category
 * @property GlossaryTerm $categoryTerm
 * @property GlossaryRelationship[] $relationships
 * @property GlossaryRelationship[] $inverseRelationships
 * @property GlossaryTermImage[] $images
 */
class GlossaryTerm extends Model
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
    protected $table = 'terms';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('vicflora', function (Builder $builder) {
            $builder->where('terms.glossary_id', 4);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(GlossaryCategory::class, 'category_id');
    }

    /**
     * Undocumented function
     *
     * @return \App\Models\GlossaryTerm|null
     */
    public function getCategoryTermAttribute()
    {
        $category = $this->category;
        if ($category) {
            return GlossaryTerm::find($category->term_id);
        }
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relationships(): HasMany
    {
        return $this->hasMany(GlossaryRelationship::class, 'term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inverseRelationships(): HasMany
    {
        return $this->hasMany(GlossaryRelationship::class, 'related_term_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(GlossaryTermImage::class, 'term_id');
    }

}
