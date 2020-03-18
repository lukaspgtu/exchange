<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public static function settings()
    {
        return System::first();
    }

    public static function marketBuyPrice()
    {
        $system = System::first();

        $ticker = HistoryTicker::last();

        $brl = $ticker->usd * $ticker->btc;

        $price = $brl + fee($brl, $system->market_buy_price);

        return $price;
    }

    public static function marketSalePrice()
    {
        $system = System::first();

        $ticker = HistoryTicker::last();

        $brl = $ticker->usd * $ticker->btc;

        $price = $brl - fee($brl, $system->market_sale_price);

        return $price;
    }

    public static function platformBuyPrice()
    {
        $system = System::first();

        $ticker = HistoryTicker::last();

        $brl = $ticker->usd * $ticker->btc;

        $price = $brl + fee($brl, $system->platform_buy_price);

        return $price;
    }

    public static function platformSalePrice()
    {
        $system = System::first();

        $ticker = HistoryTicker::last();

        $brl = $ticker->usd * $ticker->btc;

        $price = $brl - fee($brl, $system->platform_sale_price);

        return $price;
    }
}
