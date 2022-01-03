<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapperAgent extends Model
{
    protected $connection = 'mapper';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agents';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['created_at', 'updated_at', 'name', 'givenName', 'familyName', 'initials', 'email'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assertions()
    {
        return $this->hasMany('App\Models\Assertion');
    }
}
