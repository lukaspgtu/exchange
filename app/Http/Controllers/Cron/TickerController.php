<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use AjaxJSON;
use App\HistoryTicker;

class TickerController extends Controller
{
    public function setHistory()
    {
        $ajaxJSON = new AjaxJSON();

        $res = $ajaxJSON->get('https://ticker.proexbit.com');

        dd($res->data);

        HistoryTicker::create([
            'usd' => $res['data']['USD'],
            'btc' => $res['data']['BTC']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Histórico criado com sucesso!'
        ]);
    }
}
