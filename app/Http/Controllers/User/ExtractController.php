<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExtractController extends Controller
{
    public function allExtracts()
    {
        $user = Auth::user();

        $extracts = $user->getExtracts();

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function buyExtracts()
    {
        $user = Auth::user();

        $extracts = $user->getExtractsByType(BUY);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function buyFeeExtracts()
    {
        $user = Auth::user();

        $extracts = $user->getExtractsByType(BUY_FEE);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function saleExtracts()
    {
        $user = Auth::user();

        $extracts = $user->getExtractsByType(SALE);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function saleFeeExtracts()
    {
        $user = Auth::user();

        $extracts = $user->getExtractsByType(SALE_FEE);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }
}
