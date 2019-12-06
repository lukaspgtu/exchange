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

    public function buyLimitedPrice(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'unit_price' => 'required'
        ]);

        $order = new Order([
            'amount' => $request->amount,
            'unit_price' => $request->unit_price,
            'type' => 'buy'
        ]);

        $order->total = $order->amount / $order->unit_price;

        $order->tax();

        $order->order();

        return response()->json([
            'total' => formatBitcoin($order->total),
            'position' => $order->position,
            'fee' => satoshi_to_bitcoin($order->fee)
        ]);
    }

    public function saleLimitedPrice(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'unit_price' => 'required'
        ]);

        $order = new Order([
            'amount' => $request->amount,
            'unity_price' => $request->unit_price,
            'type' => 'sale'
        ]);

        $order->total = formatReal($order->amount * $order->unit_price);

        $order->tax();

        $order->order();

        return response()->json([
            'total' => $order->total,
            'position' => $order->position,
            'fee' => $order->fee
        ]);
    }

    public function buyMarketPrice(Request $request)
    {
        $request->validate([
            'amount' => 'required'
        ]);

        $firstOrder = Order::where('type', 'buy')
            ->where('position', 1)
            ->first();

        if ($firstOrder != null)
            $unit_price = formatReal($firstOrder->unit_price + 1.00);

        else
            $unit_price = formatReal(System::bitcoinBuy());

        $order = new Order([
            'amount' => $request->amount,
            'unit_price' => $unit_price,
            'position' => 1,
            'type' => 'buy'
        ]);

        $order->total = formatBitcoin($order->amount / $order->unit_price);

        $order->tax();

        return response()->json([
            'unit_price' => $order->unit_price,
            'total' => $order->total,
            'position' => $order->position,
            'fee' => $order->fee
        ]);

    }

    public function saleMarketPrice(Request $request)
    {
        $request->validate([
            'amount' => 'required'
        ]);

        $firstOrder = Order::where('type', 'sale')
            ->where('position', 1)
            ->first();

        if ($firstOrder != null)
            $unit_price = formatReal($firstOrder->unit_price - 1.00);

        else
            $unit_price = formatReal(System::bitcoinBuy());

        $order = new Order([
            'amount' => $request->amount,
            'unit_price' => $unit_price,
            'position' => 1,
            'type' => 'sale'
        ]);

        $order->total = formatReal($order->amount * $order->unit_price);

        $order->tax();

        return response()->json([
            'unit_price' => $order->unit_price,
            'total' => $order->total,
            'position' => $order->position,
            'fee' => $order->fee
        ]);

    }
}
