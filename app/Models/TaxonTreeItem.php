<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $taxon_concept_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $version
 * @property integer $parent_id
 * @property int $node_number
 * @property int $highest_descendant_node_number
 * @property integer $depth
 * @property string $path
 * @property string $name_path
 * 
 * @property Agent $createdBy
 * @property Agent $modifiedBy
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'taxon_concept_id', 
            'created_at', 'updated_at', 'version', 'parent_id', 'node_number', 
            'highest_descendant_node_number', 'depth', 'path', 'name_path'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }
}
