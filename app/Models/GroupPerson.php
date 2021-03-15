<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $groupId
 * @property integer $memberId
 * @property string $createdAt
 * @property string $updatedAt
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'group_id', 'member_id', 'created_at', 'updated_at', 'version', 'sequence'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('App\Models\Agent', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('App\Models\Agent', 'group_id');
    }
}
