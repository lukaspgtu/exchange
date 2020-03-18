<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PlatformMarket;
use App\System;
use Illuminate\Support\Facades\Auth;

class PlatformMarketController extends Controller
{
    public function simulateBuy(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $unit_price = System::platformBuyPrice();

        $platformMarket = new PlatformMarket([
            'amount' => $request->amount,
            'type' => BUY,
            'unit_price' => $unit_price
        ]);

        $platformMarket->tax();

        $value = real_to_bitcoin($request->amount, $unit_price);

        $fee = satoshi_to_bitcoin($platformMarket->fee);

        $total = $value - $fee;

        return response()->json([
            'unit_price' => $unit_price,
            'value' => $value,
            'fee' => $fee,
            'total' => $total
        ]);
    }

    public function simulateSale(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $unit_price = System::platformSalePrice();

        $platformMarket = new PlatformMarket([
            'amount' => $request->amount,
            'type' => SALE,
            'unit_price' => $unit_price
        ]);

        $platformMarket->tax();

        $value = satoshi_to_real($request->amount, $unit_price);

        $fee = $platformMarket->fee;

        $total = $value - $fee;

        return response()->json([
            'unit_price' => $unit_price,
            'value' => $value,
            'fee' => $fee,
            'total' => $total
        ]);
    }

    public function buy(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $unit_price = System::platformBuyPrice();

        $platformMarket = new PlatformMarket([
            'user_id' => Auth::id(),
            'type' => BUY,
            'amount' => $request->amount,
            'unit_price' => $unit_price
        ]);

        $platformMarket->tax();

        $platformMarket->setTickerEarning();

        $platformMarket->save();

        return response()->json([
            'success' => true,
            'message' => 'Compra realizada com sucesso!'
        ]);
    }

    public function sale(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $unit_price = System::platformSalePrice();

        $platformMarket = new PlatformMarket([
            'user_id' => Auth::id(),
            'type' => SALE,
            'amount' => $request->amount,
            'unit_price' => $unit_price
        ]);

        $platformMarket->tax();

        $platformMarket->setTickerEarning();

        $platformMarket->save();

        return response()->json([
            'success' => true,
            'message' => 'Venda realizada com sucesso!'
        ]);
    }
}
