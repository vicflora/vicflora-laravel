<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $term_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $value
 * @property string $label
 * @property string $iri
 * @property Term $term
 * @property Assertion[] $assertions
 */
class TermValue extends Model
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
    protected $fillable = ['term_id', 'created_at', 'updated_at', 'value', 'label', 'iri'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Models\Term');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assertions()
    {
        return $this->hasMany('App\Models\Assertion');
    }
}
