<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryTicker extends Model
{
    protected $table = 'history_ticker';

    protected $fillable = [
        'usd', 'btc'
    ];
}
