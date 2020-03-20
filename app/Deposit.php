<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'type', 'email', 'amount', 'transaction_hash', 'url_receipt'
    ];
}
