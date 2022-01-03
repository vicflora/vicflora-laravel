<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $occurrence_id
 * @property integer $term_id
 * @property integer $term_value_id
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $reason
 * @property string $remarks
 * @property Occurrence $occurrence
 * @property Term $term
 * @property TermValue $termValue
 * @property Agent $agent
 */
class Assertion extends Model
{
    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mapper';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['occurrence_id', 'term_id', 'term_value_id', 'user_id', 'created_at', 'updated_at', 'reason', 'remarks', 'assertion_source_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function occurrence()
    {
        return $this->belongsTo('App\Models\Occurrence', null, 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Models\Term');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function termValue()
    {
        return $this->belongsTo('App\Models\TermValue');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('App\Models\Agent');
    }
}
