<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\AjaxJSON;
use App\HistoryTicker;
use App\System;

class TickerController extends Controller
{
    public function setHistory()
    {
        $settings = System::settings();

        $ajaxJSON = new AjaxJSON();

        $res = $ajaxJSON->get('https://api.prosystemsc.com/ticker');

        $price = $res->rates->USDCBRL * $res->rates->BITCOIN;

        $market_buy_price = $price + fee($price, $settings->market_buy_price);

        $market_sale_price = $price - fee($price, $settings->market_sale_price);

        $platform_buy_price = $price + fee($price, $settings->platform_buy_price);

        $platform_sale_price = $price - fee($price, $settings->platform_sale_price);

        HistoryTicker::create([
            'market_buy_price' => $market_buy_price,
            'market_sale_price' => $market_sale_price,
            'platform_buy_price' => $platform_buy_price,
            'platform_sale_price' => $platform_sale_price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Histórico criado com sucesso!'
        ]);
    }
}
