<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $img_src
 * @property string $text
 */
class Highlight extends BaseModel
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
    protected $fillable = ['created_at', 'updated_at', 'img_src', 'text'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = true;

}
