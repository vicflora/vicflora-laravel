<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected $fillable = ['agent_type_id', 'created_by_id', 'modified_by_id', 
            'created_at', 'updated_at', 'name', 'first_name', 'last_name', 
            'initials', 'email', 'legal_name', 'guid', 'version'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agentType(): BelongsTo
    {
        return $this->belongsTo(AgentType::class);
    }

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupPerson::class, 'group_id')->orderBy('sequence');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string|null
     */
    public function getAgentTypeNameAttribute(): ?string
    {
        if ($this->agent_type_id) {
            $at = AgentType::find($this->agent_type_id);
            return $at->name;
        }
        return null;
    }

    /**
     * @param string|null $value
     * @return void
     */
    public function setAgentTypeNameAttribute($value)
    {
        if ($value) {
            $at = AgentType::where('name', $value)->first();
            $this->agent_type_id = $at->id;
        }
    }
}
