<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Google2FA;

class Google2FAController extends Controller
{
    public function qrcode2FA()
    {
        $user = Auth::user();

        if ($user->twofactor_status == 'disabled') {

            $qrcode = Google2FA::getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->twofactor_key
            );

            return response()->json([
                'success' => true,
                'qrcode' => $qrcode
            ]);

        }

        return response()->json([
            'success' => true,
            'qrcode' => 'enabled'
        ]);

    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::user();

        if ($user->twofactor_status == 'enabled') {

            return response()->json([
                'success' => false,
                'message' => 'Autenticação de dois fatores já está ativada!'
            ]);

        }

        if (Google2FA::verifyGoogle2FA($user->twofactor_key, $request->code)) {

            $user->twofactor_status = 'enabled';

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Autenticação de dois fatores ativada com sucesso!'
            ]);

        }

        return response()->json([
            'success' => false,
            'message' => 'Código inválido!'
        ]);
    }
}
