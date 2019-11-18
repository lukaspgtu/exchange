<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\System;

class OrderController extends Controller
{
    public function buy(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'type' => 'required|string',
            'amount' => 'required',
            'fee' => 'required',
            'unit_price' => 'required'
        ]);


    }

    public function sell(Request $request)
    {

    }

    public function simulateBuy()
    {
        $system = System::getData();

        dd($system);
    }

    public function simulateSale()
    {

    }

    public function bitcoinPrice()
    {

    }
}
