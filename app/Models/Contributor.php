<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $reference_id
 * @property integer $agent_id
 * @property integer $contributor_role_idd
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $created_at
 * @property string $updated_at
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
    protected $fillable = ['reference_id', 'agent_id', 'contributor_role_id', 
            'created_by_id', 'modified_by_id', 'created_at', 'updated_at', 
            'sequence', 'guid', 'version'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contributorRole(): BelongsTo
    {
        return $this->belongsTo(ContributorRole::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }
}
