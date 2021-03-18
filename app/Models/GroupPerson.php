<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $group_id
 * @property integer $member_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $version
 * @property integer $sequence
 * @property Agent $group
 * @property Agent $member
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 */
class GroupPerson extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'group_id', 
            'member_id', 'created_at', 'updated_at', 'version', 'sequence'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'group_id');
    }
}
