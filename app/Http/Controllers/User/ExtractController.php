<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;

class ExtractController extends Controller
{
    public function allExtracts()
    {
        $user = User::find(Auth::id());

        $extracts = $user->getExtracts();

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function buyExtracts()
    {
        $user = User::find(Auth::id());

        $extracts = $user->getExtractsByType(BUY);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function buyFeeExtracts()
    {
        $user = User::find(Auth::id());

        $extracts = $user->getExtractsByType(BUY_FEE);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function saleExtracts()
    {
        $user = User::find(Auth::id());

        $extracts = $user->getExtractsByType(SALE);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }

    public function saleFeeExtracts()
    {
        $user = User::find(Auth::id());

        $extracts = $user->getExtractsByType(SALE_FEE);

        return response()->json([
            'success' => true,
            'data' => $extracts
        ]);
    }
}
