<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $iri
 * @property TermValue[] $termValues
 * @property Assertion[] $assertions
 */
class Term extends Model
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
    protected $fillable = ['created_at', 'updated_at', 'name', 'iri'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function termValues()
    {
        return $this->hasMany('App\Models\TermValue');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assertions()
    {
        return $this->hasMany('App\Models\Assertion');
    }
}
