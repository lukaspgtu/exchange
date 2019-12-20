<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;

class Session extends Model
{
    protected $table = 'sessions_log';

    protected $fillable = [
        'user_id', 'ip', 'device', 'platform', 'browser', 'location', 'jwt_token'
    ];

    public static function logout($jwt_token)
    {
        if (Session::where('jwt_token', $jwt_token)->count()) {

            JWTAuth::invalidate($jwt_token);

            return true;

        }

        return false;
    }
}
