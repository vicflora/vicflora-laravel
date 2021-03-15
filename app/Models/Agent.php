<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $agentTypeId
 * @property integer $createdById
 * @property integer $modifiedById
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $name
 * @property string $firstName
 * @property string $lastName
 * @property string $initials
 * @property string $email
 * @property string $legalName
 * @property string $guid
 * @property integer $version
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property AgentType $agentType
 */
class Agent extends BaseModel
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
    protected $fillable = ['agent_type_id', 'created_by_id', 'modified_by_id', 'created_at', 'updated_at', 'name', 'first_name', 'last_name', 'initials', 'email', 'legal_name', 'guid', 'version'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agentType()
    {
        return $this->belongsTo('App\Models\AgentType');
    }
}
