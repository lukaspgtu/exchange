<?php

namespace App\Http\Controllers\User;

use App\Deposit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\OperationalLimit;
use App\Session;
use App\User;

class AccountController extends Controller
{
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'twofactor' => 'required'
        ]);

        if (!validateEmail($request->email)) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail inválido!'
            ]);

        }

        $user = Auth::user();

        $isUniqueEmail = !User::where('id', '<>', $user->id)
            ->where('email', $request->email)
            ->count();

        if (!$isUniqueEmail) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail indisponível!'
            ]);

        }

        if (!$user->isCorrectPassword($request->password)) {

            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta!'
            ]);

        }

        if (!$user->isCorrectTwoFactor($request->twofactor)) {

            return response()->json([
                'success' => false,
                'message' => 'Autenticação dois fatores inválida!'
            ]);

        }

        $user->email = $request->email;

        $user->createId();

        $user->save();

        $authorization = $request->header('authorization');

        $jwt_token = $user->updateJwtToken($authorization);

        return response()->json([
            'success' => true,
            'message' => 'E-mail atualizado com sucesso!',
            'token' => $jwt_token
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string',
            'twofactor' => 'required'
        ]);

        $user = Auth::user();

        if (!$user->isCorrectPassword($request->current_password)) {

            return response()->json([
                'success' => false,
                'message' => 'Senha atual incorreta!'
            ]);

        }

        if (!$user->isCorrectTwoFactor($request->twofactor)) {

            return response()->json([
                'success' => false,
                'message' => 'Autenticação dois fatores inválida!'
            ]);

        }

        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Senha atualizada com sucesso!'
        ]);
    }

    public function operationalLimits()
    {
        $limits = OperationalLimit::all();

        return response()->json([
            'success' => true,
            'data' => $limits
        ]);
    }

    public function sessions()
    {
        $sessions = Session::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    public function confirmBitcoin(Request $request)
    {
        if($request->ip() == '142.93.242.55') {

            if ($request->confirmations >= 3) {

                $user = User::where('wallet_BTC', $request->address)->first();

                $isConfirmed = Deposit::where('user_id', $user->id)
                    ->where('transaction_hash', $request->transaction_hash)
                    ->count() > 0;

                if ($isConfirmed) {

                    return response()->json([
                        'success' => true,
                        'message' => 'Depósito já confirmado!'
                    ]);

                }

                Deposit::create([
                    'user' => $user->id,
                    'type' => DEPOSIT_SOPAGUE,
                    'amount' => $request->value,
                    'transaction_hash' => $request->transaction_hash
                ]);

                $user->balance_BTC += $request->value;

                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Depósito confirmado com sucesso!'
                ]);
            }

        }
    }
}
