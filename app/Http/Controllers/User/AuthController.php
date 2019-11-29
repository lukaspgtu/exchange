<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Rules\AccountType;
use App\Rules\CNPJ;
use App\Rules\CPF;
use App\Rules\DocumentType;
use App\Session;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Location;
use WSSC\WebSocketClient;
use \WSSC\Components\ClientConfig;

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

        $user->save();

        $user->generateCode();

        $data = [
            'user' => $user,
            'location' => Location::get($request->ip())
        ];

        $message = view('mail.confirmEmail', $data)->render();

        Mail::to($user->email)->send(new SendMail('Confirmação de conta', $message));

        Session::init($user->id, $request->ip());

        return response()->json([
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso!',
            'token' => JWTAuth::fromUser($user)
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

        if (Hash::check($request->password, $user->password)) {

            Session::init($user->id, $request->ip());

            return response()->json([
                'success' => true,
                'message' => 'Autenticado com sucesso',
                'token' => JWTAuth::fromUser($user),
                '2fa_status' => $user['2fa_status']
            ]);

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
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {

            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'Usuário desconectado com successo'
            ]);

        }

        catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Usuário não pode ser desconectado'
            ]);

        }

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
                'message' => 'Usuário não encontrado!'
            ]);

        }

        $user->generateCode();

        $data = [
            'user' => $user,
            'location' => Location::get($request->ip())
        ];

        $message = view('mail.forgotPassword', $data)->render();

        Mail::to($user->email)->send(new SendMail('Redefinição de senha', $message));

        return response()->json([
            'success' => true,
            'message' => "Enviamos um código para $user->email"
        ]);
    }

    public function redefinePassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'code' => 'required|string'
        ]);

        $user = User::where('username', $request->username)
            ->where('code', $request->code)
            ->first();

        if ($user == null) {

            return response()->json([
                'success' => false,
                'message' => 'Código inválido',
            ]);
        }

        $jwt_token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Código verificado com sucesso!',
            'token' => $jwt_token,
        ]);
    }

    public function sendConfirmEmail(Request $request)
    {
        $user = Auth::user();

        $data = [
            'user' => $user,
            'location' => Location::get($request->ip())
        ];

        $message = view('mail.confirmEmail', $data)->render();

        Mail::to($user->email)->send(new SendMail('Confirmação de conta', $message));

        return response()->json([
            'success' => true,
            'message' => "Confirmação de e-mail enviada para $user->email"
        ]);
    }

    public function activateAccount($code)
    {
        $user = User::where(DB::raw('md5(code)'), $code)->first();

        if ($user == null) {

            return response()->json([
                'success' => false,
                'message' => 'Código inválido!'
            ]);
        }

        $user->email_status = 'confirmed';

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Conta ativada com sucesso!'
        ]);

    }

    public function auth()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }

    public function socket()
    {
        $client = new WebSocketClient('ws://localhost:3000', new ClientConfig());
        $client->send(json_encode(['order' => 19491]));
        // echo $client->receive();
    }
}
