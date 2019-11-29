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

        $fee = feeBTC(toBTC($request->amount, $request->unit_price), System::feeBuy());

        $position = Order::positionBuy($request->unit_price);

        $order = new Order([
            'id_user' => Auth::id(),
            'type' => 'buy',
            'amount' => $request->amount,
            'fee' => $fee,
            'unit_price' => $request->unit_price,
            'position' => $position
        ]);

        $order->save();

        $order->reorder();

        $order->process_buy();

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

        $fee = feeBRL(toBRL($request->amount, $request->unit_price), System::feeSale());

        $position = Order::positionSale($request->unit_price);

        $order = new Order([
            'id_user' => Auth::id(),
            'type' => 'sale',
            'amount' => $request->amount,
            'fee' => $fee,
            'unit_price' => $request->unit_price,
            'position' => $position
        ]);

        $order->save();

        $order->reorder();

        $order->process_sale();

        return response()->json([
            'success' => true,
            'message' => 'Ordem de venda realizada com sucesso!'
        ]);
    }

    public function simulateBuy(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'unit_price' => 'required'
        ]);

        $bitcoin_price = System::bitcoinBuy();

        $total = formatBTC($request->amount / $bitcoin_price);

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
