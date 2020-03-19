<?php

namespace App\Http\Controllers\User;

use App\HistoryTicker;
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

        $ticker = HistoryTicker::last();

        $platformMarket = new PlatformMarket([
            'amount' => $request->amount,
            'type' => BUY,
            'unit_price' => $ticker->platform_buy_price
        ]);

        $platformMarket->tax();

        $value = real_to_bitcoin($request->amount, $ticker->platform_buy_price);

        $fee = satoshi_to_bitcoin($platformMarket->fee);

        $total = $value - $fee;

        return response()->json([
            'success' => true,
            'data' => [
                'unit_price' => $ticker->platform_buy_price,
                'value' => $value,
                'fee' => $fee,
                'total' => $total
            ]
        ]);
    }

    public function simulateSale(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $ticker = HistoryTicker::last();

        $platformMarket = new PlatformMarket([
            'amount' => bitcoin_to_satoshi($request->amount),
            'type' => SALE,
            'unit_price' => $ticker->platform_buy_price
        ]);

        $platformMarket->tax();

        $value = bitcoin_to_real($request->amount, $ticker->platform_buy_price);

        $fee = $platformMarket->fee;

        $total = $value - $fee;

        return response()->json([
            'success' => true,
            'data' => [
                'unit_price' => $ticker->platform_buy_price,
                'value' => $value,
                'fee' => $fee,
                'total' => $total
            ]
        ]);
    }

    public function buy(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $ticker = HistoryTicker::last();

        $platformMarket = new PlatformMarket([
            'user_id' => Auth::id(),
            'type' => BUY,
            'amount' => $request->amount,
            'unit_price' => $ticker->platform_buy_price
        ]);

        if (!$this->obeysMinimumPrice()) {

            $settings = System::settings();

            return response()->json([
                'success' => false,
                'message' => 'Valor mínimo deve ser maior ou igual a R$ '
                    . number_format($settings->min_amount_buy, 2, ',', '.')
            ]);
        }

        if (!$this->userHasBalance()) {

            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente!'
            ]);
        }

        $platformMarket->tax();

        $platformMarket->setTickerEarning();

        $platformMarket->updateUserBalance();

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

        $ticker = HistoryTicker::last();

        $platformMarket = new PlatformMarket([
            'user_id' => Auth::id(),
            'type' => SALE,
            'amount' => bitcoin_to_satoshi($request->amount),
            'unit_price' => $ticker->platform_sale_price
        ]);

        if (!$this->obeysMinimumPrice()) {

            $settings = System::settings();

            return response()->json([
                'success' => false,
                'message' => 'Altere a valor para que o total a receber seja maior ou igual a R$ '
                    . number_format($settings->min_amount_sale, 2, ',', '.')
            ]);
        }

        if (!$this->userHasBalance()) {

            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente!'
            ]);
        }

        $platformMarket->tax();

        $platformMarket->setTickerEarning();

        $platformMarket->updateUserBalance();

        $platformMarket->save();

        return response()->json([
            'success' => true,
            'message' => 'Venda realizada com sucesso!'
        ]);
    }
}
