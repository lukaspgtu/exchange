<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'account_type', 'document_number', 'document_date', 'password', 'twofactor_key'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'balance_BTC' => 0,
        'balance_use_BTC' => 0,
        'balance_BRL' => 0.00,
        'balance_use_BRL' => 0.00,
        'operational_limit' => 1,
        'twofactor_status' => 'disabled',
        'email_status' => 'unconfirmed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'twofactor_key'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function generateCode()
    {
        $code = sprintf("%06d", mt_rand(1, 999999999));

        $this->code = substr($code, strlen($this->id)) . $this->id;

        $this->save();
    }
}
