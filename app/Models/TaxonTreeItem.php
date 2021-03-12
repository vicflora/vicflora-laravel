<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $taxonConceptId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $version
 * @property integer $parentId
 * @property int $nodeNumber
 * @property int $highestDescendantNodeNumber
 * @property integer $depth
 * @property Agent $agent
 * @property Agent $agent
 * @property TaxonConcept $taxonConcept
 */
class TaxonTreeItem extends BaseModel
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_concept_id', 'created_at', 'updated_at', 'version', 'parent_id', 'node_number', 'highest_descendant_node_number', 'depth'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept');
    }
}
