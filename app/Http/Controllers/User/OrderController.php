<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rules\OrderType;
use App\Rules\OrderPrice;
use App\Rules\OrderAmount;
use App\System;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function buy(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'type' => ['required', new OrderType],
            'amount' => ['required', new OrderAmount],
            'unit_price' => ['required', new OrderPrice]
        ]);

        $bitcoin_price = System::bitcoinBuy();

        $total = formatBitcoin($request->amount / $bitcoin_price);

        $fee = fee($total, System::feeBuy(), 8);

        $position = Order::positionBuy($request->unit_price);

        $order = new Order([
            'id_user' => Auth::id(),
            'category' => 'buy',
            'type' => $request->type,
            'amount' => $request->amount,
            'fee' => fee($total, $fee, 8),
            'unit_price' => $request->unit_price,
            'position' => $position,
            'bitcoin_price' => $bitcoin_price
        ]);

        $order->save();

        $order->reorder();

        return response()->json([
            'success' => true,
            'message' => 'Compra realizada com sucesso!'
        ]);
    }

    public function sell(Request $request)
    {

    }

    public function simulateBuy(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'type' => ['required', new OrderType],
            'unit_price' => 'required'
        ]);

        $bitcoin_price = System::bitcoinBuy();

        $total = formatBitcoin($request->amount / $bitcoin_price);

        $position = Order::positionBuy($request->unit_price);

        $fee = fee($total, System::feeBuy(), 8);

        return response()->json([
            'status' => true,
            'data' => [
                'total' => $total,
                'position' => $position,
                'fee' => $fee
            ]
        ]);
    }

    public function simulateSale()
    {

    }

    public function bitcoinPrice()
    {

    }
}
