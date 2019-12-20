<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationalLimit extends Model
{
    public $timestamps = false;

    protected $table = 'operational_limits';

    protected $fillable = [
        'id', 'description', 'deposit_BRL', 'withdrawal_BRL', 'deposit_BTC', 'withdrawal_BTC'
    ];
}
