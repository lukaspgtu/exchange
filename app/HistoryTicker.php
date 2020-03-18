<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryTicker extends Model
{
    protected $table = 'history_ticker';

    const UPDATED_AT = null;

    protected $fillable = [
        'market_buy_price', 'market_sale_price', 'platform_buy_price', 'platform_sale_price'
    ];

    public static function last()
    {
        return HistoryTicker::orderBy('created_at', 'desc')->first();
    }

    public static function last24H()
    {
        $date = date('Y-m-d H:i:s', strtotime('-1 day'));

        $history = HistoryTicker::where('created_at', '>=', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        $data = [];

        foreach ($history as $key => $ticker) {

            if ($key > 0) {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'buy_variation' => variation($ticker->platform_buy_price, $history[0]->platform_buy_price),
                    'sale_price' => $ticker->platform_sale_price,
                    'sale_variation' => variation($ticker->platform_sale_price, $history[0]->platform_sale_price),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'sale_price' => $ticker->platform_sale_price,
                    'created_at' => $ticker->created_at
                ];
            }
        }

        return $data;
    }

    public static function last1M()
    {
        $date = date('Y-m-d H:i:s', strtotime('-1 month'));

        $history = HistoryTicker::where('created_at', '>=', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        $data = [];

        foreach ($history as $key => $ticker) {

            if ($key > 0) {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'buy_variation' => variation($ticker->platform_buy_price, $history[0]->platform_buy_price),
                    'sale_price' => $ticker->platform_sale_price,
                    'sale_variation' => variation($ticker->platform_sale_price, $history[0]->platform_sale_price),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'sale_price' => $ticker->platform_sale_price,
                    'created_at' => $ticker->created_at
                ];
            }
        }

        return $data;
    }

    public static function last3M()
    {
        $date = date('Y-m-d H:i:s', strtotime('-3 month'));

        $history = HistoryTicker::where('created_at', '>=', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        $data = [];

        foreach ($history as $key => $ticker) {

            if ($key > 0) {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'buy_variation' => variation($ticker->platform_buy_price, $history[0]->platform_buy_price),
                    'sale_price' => $ticker->platform_sale_price,
                    'sale_variation' => variation($ticker->platform_sale_price, $history[0]->platform_sale_price),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'sale_price' => $ticker->platform_sale_price,
                    'created_at' => $ticker->created_at
                ];
            }
        }

        return $data;
    }

    public static function last1Y()
    {
        $date = date('Y-m-d H:i:s', strtotime('-1 year'));

        $history = HistoryTicker::where('created_at', '>=', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        $data = [];

        foreach ($history as $key => $ticker) {

            if ($key > 0) {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'buy_variation' => variation($ticker->platform_buy_price, $history[0]->platform_buy_price),
                    'sale_price' => $ticker->platform_sale_price,
                    'sale_variation' => variation($ticker->platform_sale_price, $history[0]->platform_sale_price),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data[] = [
                    'buy_price' => $ticker->platform_buy_price,
                    'sale_price' => $ticker->platform_sale_price,
                    'created_at' => $ticker->created_at
                ];
            }
        }

        return $data;
    }
}
