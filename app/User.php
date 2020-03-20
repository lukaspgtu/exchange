<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Jenssegers\Agent\Agent;
use App\Mail\SendMail;
use Google2FA;
use Location;
use App\Utils\AjaxJSON;
use Exception;
use JWTAuth;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'email', 'account_type', 'document_number', 'document_date', 'wallet_BTC', 'password', 'twofactor_key'
    ];

    protected $attributes = [
        'balance_BTC' => 0,
        'balance_use_BTC' => 0,
        'balance_BRL' => 0.00,
        'balance_use_BRL' => 0.00,
        'operational_limit' => 1,
        'twofactor_status' => 'disabled',
        'email_status' => 'unconfirmed'
    ];

    protected $hidden = [
        'password', 'twofactor_key'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function createId()
    {
        $this->id = md5(uniqid($this->email));
    }

    public function createWalletBTC()
    {
        try {

            if ($this->wallet_BTC == null) {

                $ajaxJSON = new AjaxJSON();

                $ajaxJSON->setHeaders(['Auth' => env('PROSYSTEM_API_GUID')]);

                $res = $ajaxJSON->post('https://api.prosystemsc.com/bitcoin/create');

                if ($res->success) {

                    $this->wallet_BTC = $res->address;

                }

            }

        } catch (Exception $e) {}
    }

    public function createTwofactorKey()
    {
        if ($this->twofactor_key == null) {
            $this->twofactor_key = Google2FA::generateSecretKey();
        }
    }

    public function sendConfirmationEmail()
    {
        try {

            $data = ['user' => $this];

            $message = view('mail.confirmEmail', $data)->render();

            Mail::to($this->email)->send(new SendMail('Confirmação de conta', $message));

        } catch (Exception $e) {}
    }

    public function sendForgotPassword()
    {
        try {

            $location = Location::get($_SERVER['REMOTE_ADDR']);

            $agent = new Agent();

            $data = [
                'user' => $this,
                'location' => $location ? "$location->countryName, $location->cityName - $location->regionName" : null,
                'device' => $agent->device(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
                'ip' => $_SERVER['REMOTE_ADDR']
            ];

            $message = view('mail.forgotPassword', $data)->render();

            Mail::to($this->email)->send(new SendMail('Recuperação de senha', $message));

        } catch (Exception $e) {}
    }

    public function sendNewPassword()
    {
        try {

            $new_password = generatePasswd();

            $this->password = bcrypt($new_password);

            $this->save();

            $data = [
                'user' => $this,
                'new_password' => $new_password
            ];

            $message = view('mail.newPassword', $data)->render();

            Mail::to($this->email)->send(new SendMail('Nova senha', $message));

        } catch (Exception $e) {}
    }

    public function confirmateAccount()
    {
        $this->email_status = CONFIRMED;

        $this->save();
    }

    public function createSession()
    {
        $jwt_token = JWTAuth::fromUser($this);

        $ip = $_SERVER['REMOTE_ADDR'];

        $agent = new Agent();

        $session = Session::where('user_id', $this->id)
            ->where('ip', $ip)
            ->where('device', $agent->device())
            ->where('platform', $agent->platform())
            ->where('browser', $agent->browser())
            ->first();

        if ($session == null) {

            $location = Location::get($ip);

            if ($location) {

                $fullLocation = "$location->countryName, $location->cityName - $location->regionName";

            }

            else {

                $fullLocation = 'Não encontrado';

            }

            Session::create([
                'user_id' => $this->id,
                'ip' => $ip,
                'device' => $agent->device(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
                'location' => $fullLocation,
                'jwt_token' => $jwt_token
            ]);

        }

        else {

            $session->update([
                'jwt_token' => $jwt_token
            ]);

        }

        return $jwt_token;
    }

    public function isCorrectPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    public function isCorrectTwoFactor($code)
    {
        return Google2FA::verifyGoogle2FA($this->twofactor_key, $code);
    }

    public function prepareBalances()
    {
        $this->balance_BTC = satoshi_to_bitcoin($this->balance_BTC);

        $this->balance_use_BTC = satoshi_to_bitcoin($this->balance_use_BTC);
    }

    public function prepareWalletBTC()
    {
        if ($this->wallet_BTC == null) {
            $this->createWalletBTC();
        }
    }

    public function updateJwtToken($authorization)
    {
        $token = substr($authorization, 7);

        JWTAuth::invalidate($token);

        $new_token = JWTAuth::fromUser($this);

        Session::where('user_id', $this->id)
            ->where('jwt_token', $token)
            ->update([
                'jwt_token' => $new_token
            ]);

        return $new_token;

    }

    public function getOrders()
    {
        $amount = 'if(type="sale", convert((amount / pow(10,8)), decimal(11,8)), amount) as amount';

        $fee = 'if(type="buy", convert((fee / pow(10,8)), decimal(11,8)), fee) as fee';

        $total = 'if(type="buy", convert((amount / unit_price), decimal(11,8)), convert(((amount / pow(10,8)) * unit_price), decimal(11,2))) as total';

        $processed = 'if(type="sale", convert((processed / pow(10,8)), decimal(11,8)), processed) as processed';

        $position = 'if(position <> 0, position, null) as position';

        $raw = "created_at, type, $amount, $fee, unit_price, $total, $processed, $position, status, executed_at";

        return Order::selectRaw($raw)
            ->where('user_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrdersByStatus($status)
    {
        $amount = "if(type='sale', convert((amount / pow(10,8)), decimal(11,8)), amount) as amount";

        $fee = "if(type='buy', convert((fee / pow(10,8)), decimal(11,8)), fee) as fee";

        $total = "if(type='buy', convert((amount / unit_price), decimal(11,8)), convert(((amount / pow(10,8)) * unit_price), decimal(11,2))) as total";

        $processed = "if(type='sale', convert((processed / pow(10,8)), decimal(11,8)), processed) as processed";

        $position = "if(position <> 0, position, null) as position";

        $raw = "created_at, type, $amount, $fee, unit_price, $total, $processed, $position, status, executed_at";

        switch ($status) {

            case WAITING:

                return Order::selectRaw($raw)
                    ->where('user_id', $this->id)
                    ->where('status', 'opened')
                    ->where('processed', 0)
                    ->orderBy('created_at', 'desc')
                    ->get();

            case RUNNING:

                return Order::selectRaw($raw)
                    ->where('user_id', $this->id)
                    ->where('status', 'opened')
                    ->where('processed', '>', 0)
                    ->orderBy('created_at', 'desc')
                    ->get();

            case CONFIRMED:

                return Order::selectRaw($raw)
                    ->where('user_id', $this->id)
                    ->where('status', 'executed')
                    ->orderBy('created_at', 'desc')
                    ->get();

            case CANCELED:

                return Order::selectRaw($raw)
                    ->where('user_id', $this->id)
                    ->where('status', 'canceled')
                    ->orderBy('created_at', 'desc')
                    ->get();

        }
    }

    public function getExtracts()
    {
        return Extract::select('type', 'value', 'description', 'created_at')
            ->where('user_id', $this->id)
            ->get();
    }

    public function getExtractsByType($type)
    {
        return Extract::select('type', 'value', 'description', 'created_at')
            ->where('user_id', $this->id)
            ->where('type', $type)
            ->get();
    }
}
