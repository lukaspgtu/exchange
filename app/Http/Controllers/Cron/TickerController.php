<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use AjaxJSON;

class TickerController extends Controller
{
    public function setHistory()
    {
        $ajaxJSON = new AjaxJSON();

        $res = $ajaxJSON->get('https://ticker.proexbit.com');

        dd($res);
    }
}
