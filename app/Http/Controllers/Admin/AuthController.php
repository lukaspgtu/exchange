<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin === null) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail não encontrado!',
            ]);
        }

        if ($admin->isCorrectPassword($request->password)) {

            if ($admin->twofactor_status == 'disabled') {

                $jwt_token = $admin->createSession();

                return response()->json([
                    'success' => true,
                    'message' => 'Autenticado com sucesso',
                    'token' => $jwt_token
                ]);

            } else {

                return response()->json([
                    'success' => true,
                    'message' => 'Autenticação dois fatores requerida!',
                    'twofactor_status' => 'enabled',
                    'token' => null
                ]);
            }

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Senha Incorreta!',
            ]);
        }
    }

    public function loginTwoFactor(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'twofactor' => 'required|string'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin === null) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail não encontrado!',
            ]);
        }

        if ($admin->isCorrectPassword($request->password)) {

            if ($admin->isCorrectTwoFactor($request->twofactor)) {

                $jwt_token = $admin->createSession();

                return response()->json([
                    'success' => true,
                    'message' => 'Autenticado com sucesso',
                    'token' => $jwt_token
                ]);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Autenticação dois fatores inválida!',
                ]);
            }

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Senha Incorreta!',
            ]);
        }
    }

    public function auth()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
