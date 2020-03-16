<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryTicker extends Model
{
    protected $table = 'history_ticker';

    const UPDATED_AT = null;

    protected $fillable = [
        'usd', 'btc'
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

        $base = $history[0]->usd * $history[0]->btc;

        foreach ($history as $key => $ticker) {

            $value = $ticker->usd * $ticker->btc;

            if ($key > 0) {

                $data = [
                    'value' => $value,
                    'variation' => variation($value, $base),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data = [
                    'value' => $value,
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

        $base = $history[0]->usd * $history[0]->btc;

        foreach ($history as $key => $ticker) {

            $value = $ticker->usd * $ticker->btc;

            if ($key > 0) {

                $data = [
                    'value' => $value,
                    'variation' => variation($value, $base),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data = [
                    'value' => $value,
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

        $base = $history[0]->usd * $history[0]->btc;

        foreach ($history as $key => $ticker) {

            $value = $ticker->usd * $ticker->btc;

            if ($key > 0) {

                $data = [
                    'value' => $value,
                    'variation' => variation($value, $base),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data = [
                    'value' => $value,
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

        $base = $history[0]->usd * $history[0]->btc;

        foreach ($history as $key => $ticker) {

            $value = $ticker->usd * $ticker->btc;

            if ($key > 0) {

                $data = [
                    'value' => $value,
                    'variation' => variation($value, $base),
                    'created_at' => $ticker->created_at
                ];
            }

            else {

                $data = [
                    'value' => $value,
                    'created_at' => $ticker->created_at
                ];
            }
        }

        return $data;
    }
}
