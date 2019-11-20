<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rules\OrderType;
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
            'id_user' => 'required',
            'value' => ['required', new OrderValue],
            'type' => ['required', new OrderType],
            'unit_price' => ['required', new OrderPrice]
        ]);

        $amount = toBTC($request->value, $request->unit_price, true);

        $fee = fee($amount, System::feeBuy(), 0);

        $position = Order::positionBuy($request->unit_price);

        $order = new Order([
            'id_user' => Auth::id(),
            'category' => 'buy',
            'type' => $request->type,
            'amount' => $amount,
            'fee' => $fee,
            'unit_price' => $request->unit_price,
            'total_value' => $request->value,
            'position' => $position
        ]);

        $order->save();

        $order->reorder();

        $order->process_queue();

        return response()->json([
            'success' => true,
            'message' => 'Ordem de compra realizada com sucesso!'
        ]);
    }

    public function sale(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'amount' => ['required', new OrderAmount],
            'type' => ['required', new OrderType],
            'unit_price' => ['required', new OrderPrice]
        ]);

        $total_value = toBRL($request->amount, $request->unit_price);

        $fee = fee($total_value, System::feeSale());

        $position = Order::positionSale($request->unit_price);

        $order = new Order([
            'id_user' => Auth::id(),
            'category' => 'sale',
            'type' => $request->type,
            'amount' => $request->amount,
            'fee' => $fee,
            'unit_price' => $request->unit_price,
            'total_value' => $total_value,
            'position' => $position
        ]);

        $order->save();

        $order->reorder();

        $order->process_queue();

        return response()->json([
            'success' => true,
            'message' => 'Ordem de venda realizada com sucesso!'
        ]);
    }

    public function simulateBuy(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'type' => ['required', new OrderType],
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
