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
use AjaxJSON;
use JWTAuth;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'id', 'name', 'email', 'password', 'twofactor_status', 'type', 'status'
    ];

    protected $attributes = [
        'twofactor_status' => 'disabled',
        'status' => 'enabled'
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

    public function isCorrectPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    public function isCorrectTwoFactor($code)
    {
        return Google2FA::verifyGoogle2FA($this->twofactor_key, $code);
    }

    public function createSession()
    {
        return JWTAuth::fromUser($this);
    }
}
