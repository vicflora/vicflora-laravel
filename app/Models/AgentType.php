<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $name
 * @property string $uri
 * @property string $label
 * @property string $description
 * @property string $guid
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property Agent[] $agents
 */
class AgentType extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'created_at', 
            'updated_at', 'name', 'uri', 'label', 'description', 'guid'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agents()
    {
        return $this->hasMany(Agent::class);
    }
}
