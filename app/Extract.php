<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extract extends Model
{
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'reference_id', 'type', 'value', 'description'
    ];
}
