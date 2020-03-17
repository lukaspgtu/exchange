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

    public static function minAmountBuy()
    {
        $system = System::first();

        return $system->min_amount_buy;
    }

    public static function minAmountSale()
    {
        $system = System::first();

        return $system->min_amount_sale;
    }

    public static function marketBuyFee()
    {
        $system = System::first();

        return $system->market_buy_fee;
    }

    public static function marketSaleFee()
    {
        $system = System::first();

        return $system->market_sale_fee;
    }

    public static function platformBuyFee()
    {
        $system = System::first();

        return $system->platform_buy_fee;
    }

    public static function platformSaleFee()
    {
        $system = System::first();

        return $system->platform_sale_fee;
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
