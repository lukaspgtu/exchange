<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gain extends Model
{
    const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'earnings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'buy_id', 'sale_id', 'value'
    ];

}
