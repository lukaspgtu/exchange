<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\AjaxJSON;
use App\HistoryTicker;

class TickerController extends Controller
{
    public function setHistory()
    {
        $ajaxJSON = new AjaxJSON();

        $res = $ajaxJSON->get('https://api.prosystemsc.com/ticker');

        HistoryTicker::create([
            'usd' => $res->rates->USDCBRL,
            'btc' => $res->rates->BITCOIN
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Histórico criado com sucesso!'
        ]);
    }
}
