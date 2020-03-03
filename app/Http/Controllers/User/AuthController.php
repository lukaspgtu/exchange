<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Rules\DocumentType;
use App\Rules\AccountType;
use App\Rules\CNPJ;
use App\Rules\CPF;
use App\Session;
use App\User;

class AuthController extends Controller
{
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string'
        ]);

        if (!validateEmail($request->email)) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail inválido!'
            ]);

        }

        if (User::where('email', $request->email)->count() > 0) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail indisponível!'
            ]);

        }

        return response()->json([
            'success' => true,
            'message' => 'E-mail disponível!'
        ]);

    }

    public function verifyDocumentNumber(Request $request)
    {
        $request->validate([
            'type' => ['required', new DocumentType],
            'number' => 'required|string'
        ]);

        if ($request->type == 'cpf') {

            if (!validateCPF($request->number)) {

                return response()->json([
                    'success' => false,
                    'message' => 'CPF inválido!'
                ]);

            }

            if (User::where('document_number', $request->number)->count() > 0) {

                return response()->json([
                    'success' => false,
                    'message' => 'CPF indisponível!'
                ]);

            }

            return response()->json([
                'success' => true,
                'message' => 'CPF disponível!'
            ]);

        }

        elseif ($request->type == 'cnpj') {

            if (!validateCNPJ($request->number)) {

                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ inválido!'
                ]);

            }

            if (User::where('document_number', $request->number)->count() > 0) {

                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ indisponível!'
                ]);

            }

            return response()->json([
                'success' => true,
                'message' => 'CNPJ disponível!'
            ]);

        }

        else {

            if (User::where('document_number', $request->number)->count() > 0) {

                return response()->json([
                    'success' => false,
                    'message' => 'Passaporte indisponível!'
                ]);

            }

            return response()->json([
                'success' => true,
                'message' => 'Passaporte disponível!'
            ]);

        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'account_type' => ['required', new AccountType],
            'document_number' => 'required|string',
            'document_date' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($request->account_type == 'fisical') {

            $request->validate([
                'document_number' => [new CPF, 'unique:users']
            ]);

        }

        elseif ($request->account_type == 'legal') {

            $request->validate([
                'document_number' => [new CNPJ, 'unique:users']
            ]);

        }

        else {

            $request->validate([
                'document_number' => 'unique:users'
            ]);

        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'account_type' => $request->account_type,
            'document_number' => $request->document_number,
            'document_date' => formatDate($request->document_date, 'Y-m-d'),
            'password' => bcrypt($request->password)
        ]);

        $user->createId();

        // $user->createWalletBTC();

        $user->createTwofactorKey();

        $user->save();

        $user->sendConfirmationEmail();

        $jwt_token = $user->createSession();

        return response()->json([
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso!',
            'token' => $jwt_token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user === null) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail não encontrado!',
            ]);

        }

        if ($user->isCorrectPassword($request->password)) {

            if ($user->twofactor_status == 'disabled') {

                $jwt_token = $user->createSession();

                return response()->json([
                    'success' => true,
                    'message' => 'Autenticado com sucesso',
                    'token' => $jwt_token
                ]);

            }

            else {

                return response()->json([
                    'success' => true,
                    'message' => 'Autenticação dois fatores requerida!',
                    'twofactor_status' => 'enabled',
                    'token' => null
                ]);

            }

        }

        else {

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

        $user = User::where('email', $request->email)->first();

        if ($user === null) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail não encontrado!',
            ]);

        }

        if ($user->isCorrectPassword($request->password)) {

            if ($user->isCorrectTwoFactor($request->twofactor)) {

                $jwt_token = $user->createSession();

                return response()->json([
                    'success' => true,
                    'message' => 'Autenticado com sucesso',
                    'token' => $jwt_token
                ]);

            }

            else {

                return response()->json([
                    'success' => false,
                    'message' => 'Autenticação dois fatores inválida!',
                ]);

            }

        }

        else {

            return response()->json([
                'success' => false,
                'message' => 'Senha Incorreta!',
            ]);

        }

    }

    public function logout(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        if (Session::logout($request->token)) {

            return response()->json([
                'success' => true,
                'message' => 'Usuário desconectado com successo'
            ]);

        }

        return response()->json([
            'success' => false,
            'message' => 'Usuário não pode ser desconectado'
        ]);

    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user == null) {

            return response()->json([
                'success' => false,
                'message' => 'E-mail não encontrado!'
            ]);

        }

        $user->sendForgotPassword();

        return response()->json([
            'success' => true,
            'message' => "Enviamos uma mensagem para $user->email"
        ]);
    }

    public function generateNewPassword(Request $request)
    {
        $request->validate([
            'hash' => 'required|string',
            'document_number' => 'required|string',
            'twofactor' => 'required|string'
        ]);

        $user = User::find($request->hash);

        $request->document_number = removeSymbols($request->document_number);

        if ($user == null ||  $request->document_number != $user->document_number) {

            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para alterar a senha desta conta!',
            ]);
        }

        if ($user->isCorrectTwoFactor($request->twofactor)) {

            $user->sendNewPassword();

            return response()->json([
                'success' => true,
                'message' => 'Nova senha gerada com sucesso!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Autenticação dois fatores inválida!',
        ]);
    }

    public function sendConfirmEmail()
    {
        $user = User::find(Auth::id());

        $user->sendConfirmationEmail();

        return response()->json([
            'success' => true,
            'message' => "Confirmação de e-mail enviada para $user->email"
        ]);
    }

    public function activateAccount($id)
    {
        $user = User::where('id', $id)->first();

        if ($user == null) {

            return abort(404);

        }

        $user->confirmateAccount();

        return Redirect::to('http://broker.proexbit.com');
    }

    public function auth()
    {
        $columns = [
            'id', 'name', 'document_number', 'document_date', 'email', 'account_type', 'balance_BTC', 'balance_use_BTC', 'balance_BRL', 'balance_use_BRL', 'wallet_BTC', 'twofactor_status', 'email_status'
        ];

        $user = User::select($columns)
            ->where('id', Auth::id())
            ->first();

        $user->prepareBalances();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
