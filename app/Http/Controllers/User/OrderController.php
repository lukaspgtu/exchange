<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\System;
use App\User;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function allOrders()
    {
        $user = User::find(Auth::id());

        $orders = $user->getOrders();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function ordersCanceled()
    {
        $user = User::find(Auth::id());

        $orders = $user->getOrdersByStatus(CANCELED);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function ordersExecuted()
    {
        $user = User::find(Auth::id());

        $orders = $user->getOrdersByStatus(CONFIRMED);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function ordersRunning()
    {
        $user = User::find(Auth::id());

        $orders = $user->getOrdersByStatus(RUNNING);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function ordersWaiting()
    {
        $user = User::find(Auth::id());

        $orders = $user->getOrdersByStatus(WAITING);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function buy(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'unit_price' => 'required|numeric',
        ]);

        $settings = System::settings();

        if ($request->amount < $settings->min_amount_buy) {

            return response()->json([
                'success' => false,
                'message' => 'Valor mínimo deve ser maior ou igual a R$ ' . number_format($settings->min_amount_buy, 2, ',', '.')
            ]);

        }

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
            'amount' => 'required|numeric',
            'unit_price' => 'required|numeric',
        ]);

        $settings = System::settings();

        if ($request->amount * $request->unit_price < $settings->min_amount_sale) {

            return response()->json([
                'success' => false,
                'message' => 'Altere a quantidade ou preço unitário para que o total a receber seja maior ou igual a R$ ' . number_format($settings->min_amount_sale, 2, ',', '.')
            ]);

        }

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
            'amount' => 'required|numeric',
            'unit_price' => 'required|numeric',
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
            'amount' => 'required|numeric',
            'unit_price' => 'required|numeric',
        ]);

        $order = new Order([
            'amount' => $request->amount,
            'unit_price' => $request->unit_price,
            'type' => 'sale'
        ]);

        $order->total = formatReal($order->amount * $order->unit_price);

        $order->amount = bitcoin_to_satoshi($order->amount);

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
            'amount' => 'required|numeric'
        ]);

        $firstOrder = Order::where('type', 'buy')
            ->where('position', 1)
            ->first();

        if ($firstOrder != null)
            $unit_price = formatReal($firstOrder->unit_price + 1.00);

        else
            $unit_price = formatReal(System::marketBuyPrice());

        $order = new Order([
            'amount' => $request->amount,
            'unit_price' => $unit_price,
            'position' => 1,
            'type' => 'buy'
        ]);

        $order->total = $order->amount / $order->unit_price;

        $order->tax();

        return response()->json([
            'unit_price' => $order->unit_price,
            'total' => formatBitcoin($order->total),
            'position' => $order->position,
            'fee' => satoshi_to_bitcoin($order->fee)
        ]);

    }

    public function saleMarketPrice(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $firstOrder = Order::where('type', 'sale')
            ->where('position', 1)
            ->first();

        if ($firstOrder != null)
            $unit_price = formatReal($firstOrder->unit_price - 1.00);

        else
            $unit_price = formatReal(System::marketSalePrice());

        $order = new Order([
            'amount' => $request->amount,
            'unit_price' => $unit_price,
            'position' => 1,
            'type' => 'sale'
        ]);

        $order->total = formatReal($order->amount * $order->unit_price);

        $order->amount = bitcoin_to_satoshi($order->amount);

        $order->tax();

        return response()->json([
            'unit_price' => $order->unit_price,
            'total' => $order->total,
            'position' => $order->position,
            'fee' => $order->fee
        ]);

    }

    public function orderStreaming()
    {
        $buys = Order::getAllLastBuys();

        $sales = Order::getAllLastSales();

        $executeds = Order::getAllLastExecuteds();

        return response()->json([
            'success' => true,
            'data' => [
                'buys' => $buys,
                'sales' => $sales,
                'executeds' => $executeds
            ]
        ]);
    }
}
