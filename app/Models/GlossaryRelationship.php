<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $term_id
 * @property integer $related_term_id
 * @property integer $relationship_type_id
 * @property boolean $is_misapplied
 * @property boolean $is_discouraged
 * @property string $guid
 * @property integer $version
 * @property string $timestamp_created
 * @property string $timestamp_modified
 * @property GlossaryTerm $term
 * @property GlossaryTerm $relatedTerm
 * @property GlossaryRelationshipType $relationshipType
 * 
 */
class GlossaryRelationship extends Model
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
    protected $table = 'relationships';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(GlossaryTerm::class, 'term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relatedTerm(): BelongsTo
    {
        return $this->belongsTo(GlossaryTerm::class, 'related_term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relationshipType(): BelongsTo
    {
        return $this->belongsTo(GlossaryRelationshipType::class, 'relationship_type_id');
    }

}
