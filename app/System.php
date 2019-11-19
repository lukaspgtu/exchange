<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class System extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    public static function bitcoinBuy()
    {
        return DB::table('system')->first()->bitcoin_buy;
    }

    public static function bitcoinSale()
    {
        return DB::table('system')->first()->bitcoin_sale;
    }

    public static function feeBuy()
    {
        return DB::table('system')->first()->fee_buy;
    }

    public static function feeSale()
    {
        return DB::table('system')->first()->fee_sale;
    }
}
