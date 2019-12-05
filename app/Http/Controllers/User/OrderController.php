<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rules\OrderPrice;
use App\Rules\OrderAmount;
use App\Rules\OrderValue;
use App\System;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function buy(Request $request)
    {
        $request->validate([
            'amount' => ['required', new OrderValue],
            'unit_price' => ['required', new OrderPrice]
        ]);

        $user = Auth::user();

        if ($user->balance_BRL < $request->amount) {

            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente!'
            ]);

        }

        $order = new Order([
            'user_id' => $user->id,
            'type' => 'buy',
            'amount' => $request->amount,
            'unit_price' => $request->unit_price
        ]);

        $order->tax();

        $order->order();

        $order->save();

        $order->updateUserBalances();

        $order->reorder();

        $order->processBuy();

        return response()->json([
            'success' => true,
            'message' => 'Ordem de compra realizada com sucesso!'
        ]);
    }

    public function sale(Request $request)
    {
        $request->validate([
            'amount' => ['required', new OrderAmount],
            'unit_price' => ['required', new OrderPrice]
        ]);

        $user = Auth::user();

        if ($user->balance_BTC < $request->amount) {

            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente!'
            ]);

        }

        $order = new Order([
            'user_id' => $user->id,
            'type' => 'sale',
            'amount' => bitcoin_to_satoshi($request->amount),
            'unit_price' => $request->unit_price
        ]);

        $order->tax();

        $order->order();

        $order->save();

        $order->updateUserBalances();

        $order->reorder();

        $order->processSale();

        return response()->json([
            'success' => true,
            'message' => 'Ordem de venda realizada com sucesso!'
        ]);
    }

    public function simulateBuy(Request $request)
    {
        // $request->validate([
        //     'amount' => 'required',
        //     'unit_price' => 'required'
        // ]);

        // $bitcoin_price = System::bitcoinBuy();

        // $total = formatBTC($request->amount / $bitcoin_price);

        // $position = Order::positionBuy($request->unit_price);

        // $fee = fee($total, System::feeBuy(), 8);

        // return response()->json([
        //     'status' => true,
        //     'data' => [
        //         'total' => $total,
        //         'position' => $position,
        //         'fee' => $fee
        //     ]
        // ]);
    }

    public function simulateSale()
    {

    }

    public function bitcoinPrice()
    {

    }
}
