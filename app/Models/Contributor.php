<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $referenceId
 * @property integer $agentId
 * @property integer $contributorRoleId
 * @property integer $createdById
 * @property integer $modifiedById
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $sequence
 * @property string $guid
 * @property integer $version
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property ContributorRole $contributorRole
 * @property Agent $agent
 * @property Reference $reference
 */
class Contributor extends BaseModel
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
    protected $fillable = ['reference_id', 'agent_id', 'contributor_role_id', 'created_by_id', 'modified_by_id', 'created_at', 'updated_at', 'sequence', 'guid', 'version'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contributorRole()
    {
        return $this->belongsTo('App\ContributorRole');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('App\Models\Agent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference()
    {
        return $this->belongsTo('App\Models\Reference');
    }
}
