<?php

namespace App\Http\Controllers\User;

use App\HistoryTicker;
use App\Http\Controllers\Controller;

class HistoryTickerController extends Controller
{
    public function history24H()
    {
        $history = HistoryTicker::last24H();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function history1M()
    {
        $history = HistoryTicker::last1M();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function history3M()
    {
        $history = HistoryTicker::last3M();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function history1Y()
    {
        $history = HistoryTicker::last1Y();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
