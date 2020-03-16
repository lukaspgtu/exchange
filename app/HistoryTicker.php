<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryTicker extends Model
{
    protected $table = 'history_ticker';

    const UPDATED_AT = null;

    protected $fillable = [
        'usd', 'btc'
    ];
}
